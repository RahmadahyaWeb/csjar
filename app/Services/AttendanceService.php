<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\BreakRule;
use App\Models\EmployeeAssignment;
use App\Models\EmployeeSchedule;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\WorkScheduleDay;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function getTodayLogs(int $userId)
    {
        return AttendanceLog::where('user_id', $userId)
            ->whereDate('recorded_at', now()->toDateString())
            ->orderBy('recorded_at')
            ->get();
    }

    public function getState(int $userId): array
    {
        $logs = $this->getTodayLogs($userId);

        $checkin = $logs->firstWhere('type', 'checkin');
        $checkout = $logs->where('type', 'checkout')->last();

        $breakStart = $logs->where('type', 'break_start')->count();
        $breakEnd = $logs->where('type', 'break_end')->count();

        return [
            'has_checkin' => (bool) $checkin,
            'has_checkout' => (bool) $checkout,
            'is_on_break' => $breakStart > $breakEnd,
            'checkin_at' => $checkin?->recorded_at,
            'checkout_at' => $checkout?->recorded_at,
            'logs' => $logs,
        ];
    }

    // ======================
    // UI HELPERS
    // ======================
    public function canCheckIn(array $state): bool
    {
        return ! $state['has_checkin'];
    }

    public function canCheckOut(array $state): bool
    {
        return $state['has_checkin'] && ! $state['has_checkout'];
    }

    public function canStartBreak(array $state): bool
    {
        return $state['has_checkin']
            && ! $state['has_checkout']
            && ! $state['is_on_break'];
    }

    public function canEndBreak(array $state): bool
    {
        return $state['is_on_break'];
    }

    // ======================
    // ACTIONS
    // ======================
    public function checkIn(int $userId, ?float $lat, ?float $lng)
    {
        return DB::transaction(function () use ($userId, $lat, $lng) {

            $state = $this->getState($userId);

            if (! $this->canCheckIn($state)) {
                throw new Exception('Already checked in');
            }

            $this->ensureLocation($lat, $lng);
            $this->validateWorkingDay($userId);
            $this->validateHolidayToday();
            $this->validateLeaveToday($userId);
            $this->validateShiftTime($userId);
            $this->validateGps($userId, $lat, $lng);

            return AttendanceLog::create([
                'user_id' => $userId,
                'type' => 'checkin',
                'latitude' => $lat,
                'longitude' => $lng,
                'recorded_at' => now(),
            ]);
        });
    }

    public function checkOut(int $userId, ?float $lat, ?float $lng)
    {
        return DB::transaction(function () use ($userId, $lat, $lng) {

            $state = $this->getState($userId);

            if (! $this->canCheckOut($state)) {
                throw new Exception('Cannot checkout');
            }

            $this->ensureLocation($lat, $lng);
            $this->validateWorkingDay($userId);
            $this->validateHolidayToday();
            $this->validateLeaveToday($userId);
            $this->validateShiftTime($userId);
            $this->validateGps($userId, $lat, $lng);

            $log = AttendanceLog::create([
                'user_id' => $userId,
                'type' => 'checkout',
                'latitude' => $lat,
                'longitude' => $lng,
                'recorded_at' => now(),
            ]);

            $this->processDaily($userId, now()->toDateString());

            return $log;
        });
    }

    public function startBreak(int $userId, ?float $lat, ?float $lng)
    {
        $state = $this->getState($userId);

        if (! $this->canStartBreak($state)) {
            throw new Exception('Cannot start break');
        }

        $this->ensureLocation($lat, $lng);
        $this->validateGps($userId, $lat, $lng);
        $this->validateBreakRule($userId);

        return AttendanceLog::create([
            'user_id' => $userId,
            'type' => 'break_start',
            'latitude' => $lat,
            'longitude' => $lng,
            'recorded_at' => now(),
        ]);
    }

    public function endBreak(int $userId, ?float $lat, ?float $lng)
    {
        $state = $this->getState($userId);

        if (! $this->canEndBreak($state)) {
            throw new Exception('Cannot end break');
        }

        $this->ensureLocation($lat, $lng);
        $this->validateGps($userId, $lat, $lng);

        return AttendanceLog::create([
            'user_id' => $userId,
            'type' => 'break_end',
            'latitude' => $lat,
            'longitude' => $lng,
            'recorded_at' => now(),
        ]);
    }

    // ======================
    // PROCESSING
    // ======================
    public function processDaily(int $userId, string $date)
    {
        $logs = AttendanceLog::where('user_id', $userId)
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at')
            ->get();

        if (! $this->isWorkingDay($userId, $date)) {
            return null;
        }

        if ($this->isHoliday($date)) {
            return $this->store($userId, $date, ['status' => 'holiday']);
        }

        if ($this->isLeave($userId, $date)) {
            return $this->store($userId, $date, ['status' => 'leave']);
        }

        if ($logs->isEmpty()) {
            return $this->store($userId, $date, ['status' => 'absent']);
        }

        $checkin = $logs->firstWhere('type', 'checkin');
        $checkout = $logs->where('type', 'checkout')->last();

        if (! $checkin || ! $checkout) {
            return $this->store($userId, $date, ['status' => 'absent']);
        }

        $breakMinutes = min(120, $this->calculateBreakMinutes($logs));

        $workMinutes = max(0,
            Carbon::parse($checkin->recorded_at)
                ->diffInMinutes($checkout->recorded_at) - $breakMinutes
        );

        [$late, $early, $overtime] = $this->calculateShiftMetrics($userId, $checkin, $checkout);

        return $this->store($userId, $date, [
            'status' => 'present',
            'checkin_at' => $checkin->recorded_at,
            'checkout_at' => $checkout->recorded_at,
            'work_minutes' => $workMinutes,
            'break_minutes' => $breakMinutes,
            'late_minutes' => $late,
            'early_leave_minutes' => $early,
            'overtime_minutes' => $overtime,
        ]);
    }

    protected function calculateBreakMinutes($logs): int
    {
        $stack = [];
        $total = 0;

        foreach ($logs as $log) {
            if ($log->type === 'break_start') {
                $stack[] = $log;
            }

            if ($log->type === 'break_end' && ! empty($stack)) {
                $start = array_pop($stack);
                $total += Carbon::parse($start->recorded_at)
                    ->diffInMinutes($log->recorded_at);
            }
        }

        return $total;
    }

    protected function calculateShiftMetrics($userId, $checkin, $checkout): array
    {
        $shift = $this->getTodayShiftDetail($userId);

        if (! $shift) {
            return [0, 0, 0];
        }

        $date = Carbon::parse($checkin->recorded_at)->toDateString();

        $start = Carbon::parse($date.' '.$shift->start_time);
        $end = Carbon::parse($date.' '.$shift->end_time);

        if ($shift->is_overnight) {
            $end->addDay();
        }

        $rawLate = $checkin->recorded_at > $start
            ? $start->diffInMinutes($checkin->recorded_at)
            : 0;

        $late = max(0, $rawLate - ($shift->tolerance_late ?? 0));

        $rawEarly = $checkout->recorded_at < $end
            ? Carbon::parse($checkout->recorded_at)->diffInMinutes($end)
            : 0;

        $early = max(0, $rawEarly - ($shift->tolerance_early_leave ?? 0));

        $overtime = $checkout->recorded_at > $end
            ? $end->diffInMinutes($checkout->recorded_at)
            : 0;

        return [$late, $early, $overtime];
    }

    protected function store($userId, $date, array $data)
    {
        return Attendance::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            $data
        );
    }

    // ======================
    // VALIDATIONS
    // ======================
    protected function isWorkingDay(int $userId, string $date): bool
    {
        $day = $this->getWorkScheduleDay($userId, $date);

        return $day && $day->is_working_day;
    }

    protected function isHoliday(string $date): bool
    {
        return Holiday::whereDate('date', $date)->exists();
    }

    protected function isLeave(int $userId, string $date): bool
    {
        return Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }

    protected function validateHolidayToday(): void
    {
        if ($this->isHoliday(now()->toDateString())) {
            throw new Exception('Today is a holiday');
        }
    }

    protected function validateLeaveToday(int $userId): void
    {
        if ($this->isLeave($userId, now()->toDateString())) {
            throw new Exception('You are on leave');
        }
    }

    protected function validateWorkingDay(int $userId): void
    {
        if (! $this->isWorkingDay($userId, now()->toDateString())) {
            throw new Exception('Not a working day');
        }
    }

    protected function validateShiftTime(int $userId): void
    {
        $shift = $this->getTodayShiftDetail($userId);

        if (! $shift) {
            throw new Exception('No shift assigned');
        }

        $today = now()->toDateString();

        $start = Carbon::parse($today.' '.$shift->start_time);
        $end = Carbon::parse($today.' '.$shift->end_time);

        if ($shift->is_overnight) {
            $end->addDay();
        }

        $now = now();

        if ($now < $start->copy()->subMinutes($shift->tolerance_late ?? 0) || $now > $end) {
            throw new Exception('Outside shift time');
        }
    }

    protected function validateBreakRule(int $userId)
    {
        $shift = $this->getTodayShiftDetail($userId);

        if (! $shift) {
            throw new Exception('No shift assigned');
        }

        $rules = BreakRule::where('shift_id', $shift->id)->get();

        foreach ($rules as $rule) {
            if ($rule->start_time &&
                now()->format('H:i:s') >= $rule->start_time &&
                now()->format('H:i:s') <= $rule->end_time) {
                return;
            }
        }

        if ($rules->where('is_flexible', true)->isNotEmpty()) {
            return;
        }

        throw new Exception('Break not allowed');
    }

    protected function getWorkScheduleDay(int $userId, $date = null): ?WorkScheduleDay
    {
        $date = $date ?? now()->toDateString();
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso;

        $schedule = EmployeeSchedule::with('workSchedule.days.shift')
            ->where('user_id', $userId)
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->first();

        return $schedule?->workSchedule->days
            ->firstWhere('day_of_week', $dayOfWeek);
    }

    protected function getTodayShiftDetail(int $userId)
    {
        return $this->getWorkScheduleDay($userId)?->shift;
    }

    protected function validateGps(int $userId, float $lat, float $lng): void
    {
        $assignment = EmployeeAssignment::with('branch')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (! $assignment || ! $assignment->branch) {
            throw new Exception('No branch assigned');
        }

        $distance = $this->calculateDistance(
            $lat,
            $lng,
            $assignment->branch->latitude,
            $assignment->branch->longitude
        );

        if ($distance > $assignment->branch->radius) {
            throw new Exception('Outside allowed area');
        }
    }

    protected function ensureLocation(?float $lat, ?float $lng): void
    {
        if (! $lat || ! $lng) {
            throw new Exception('Location not detected');
        }
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) *
            pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
}
