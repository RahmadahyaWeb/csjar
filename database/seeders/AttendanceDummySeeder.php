<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\EmployeeSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceDummySeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            $startDate = Carbon::create(2026, 4, 1);
            $endDate = Carbon::create(2026, 4, 14);

            $users = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin');
            })->pluck('id');

            foreach ($users as $userId) {

                $schedule = EmployeeSchedule::with('workSchedule.days.shift')
                    ->where('user_id', $userId)
                    ->where('is_active', true)
                    ->first();

                if (! $schedule) {
                    continue;
                }

                $current = $startDate->copy();

                while ($current->lte($endDate)) {

                    // ======================
                    // FIX: WAJIB ISO (1-7)
                    // ======================
                    $dayIso = $current->dayOfWeekIso;

                    $dayConfig = $schedule->workSchedule->days
                        ->firstWhere('day_of_week', $dayIso);

                    if (! $dayConfig || ! $dayConfig->is_working_day) {
                        $current->addDay();

                        continue;
                    }

                    $shift = $dayConfig->shift;

                    if (! $shift) {
                        $current->addDay();

                        continue;
                    }

                    // ======================
                    // SCENARIO RANDOM
                    // ======================
                    $scenario = match (rand(1, 6)) {
                        1 => 'normal',
                        2 => 'late',
                        3 => 'early',
                        4 => 'overtime',
                        5 => 'no_break',
                        default => 'absent',
                    };

                    $lat = -3.319437;
                    $lng = 114.590752;

                    $date = $current->toDateString();

                    // ======================
                    // ABSENT
                    // ======================
                    if ($scenario === 'absent') {

                        Attendance::updateOrCreate(
                            [
                                'user_id' => $userId,
                                'date' => $date,
                            ],
                            [
                                'status' => 'absent',
                            ]
                        );

                        $current->addDay();

                        continue;
                    }

                    // ======================
                    // BASE SHIFT
                    // ======================
                    $baseStart = Carbon::parse($date.' '.$shift->start_time);
                    $baseEnd = Carbon::parse($date.' '.$shift->end_time);

                    // ======================
                    // CHECKIN
                    // ======================
                    $checkin = match ($scenario) {
                        'late' => $baseStart->copy()->addMinutes(rand(15, 45)),
                        default => $baseStart->copy()->addMinutes(rand(0, 10)),
                    };

                    AttendanceLog::create([
                        'user_id' => $userId,
                        'type' => 'checkin',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'recorded_at' => $checkin,
                    ]);

                    // ======================
                    // BREAK
                    // ======================
                    $totalBreak = 0;

                    if ($scenario !== 'no_break') {

                        $breakStart = $baseStart->copy()->addHours(4)->addMinutes(rand(0, 15));
                        $breakEnd = $breakStart->copy()->addMinutes(60);

                        AttendanceLog::insert([
                            [
                                'user_id' => $userId,
                                'type' => 'break_start',
                                'latitude' => $lat,
                                'longitude' => $lng,
                                'recorded_at' => $breakStart,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'user_id' => $userId,
                                'type' => 'break_end',
                                'latitude' => $lat,
                                'longitude' => $lng,
                                'recorded_at' => $breakEnd,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        ]);

                        $totalBreak = $breakStart->diffInMinutes($breakEnd);
                    }

                    // ======================
                    // CHECKOUT
                    // ======================
                    $checkout = match ($scenario) {
                        'early' => $baseEnd->copy()->subMinutes(rand(30, 120)),
                        'overtime' => $baseEnd->copy()->addMinutes(rand(30, 120)),
                        default => $baseEnd->copy()->addMinutes(rand(0, 15)),
                    };

                    AttendanceLog::create([
                        'user_id' => $userId,
                        'type' => 'checkout',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'recorded_at' => $checkout,
                    ]);

                    // ======================
                    // CALCULATION (SYNC SERVICE)
                    // ======================
                    $workMinutes = max(0,
                        $checkin->diffInMinutes($checkout) - $totalBreak
                    );

                    $late = $checkin->gt($baseStart)
                        ? $baseStart->diffInMinutes($checkin)
                        : 0;

                    $early = $checkout->lt($baseEnd)
                        ? $checkout->diffInMinutes($baseEnd)
                        : 0;

                    $overtime = $checkout->gt($baseEnd)
                        ? $baseEnd->diffInMinutes($checkout)
                        : 0;

                    Attendance::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'date' => $date,
                        ],
                        [
                            'status' => 'present',
                            'checkin_at' => $checkin,
                            'checkout_at' => $checkout,
                            'work_minutes' => $workMinutes,
                            'break_minutes' => $totalBreak,
                            'late_minutes' => $late,
                            'early_leave_minutes' => $early,
                            'overtime_minutes' => $overtime,
                            'is_locked' => true,
                        ]
                    );

                    $current->addDay();
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
