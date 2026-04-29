<?php

// app/Livewire/Pages/Dashboard/Index.php

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    protected $listeners = ['refreshDashboard' => '$refresh'];

    #[Computed]
    public function stats()
    {
        return [
            // GLOBAL
            'attendance_today' => Attendance::whereDate('date', now())->count(),
            'present_today' => Attendance::whereDate('date', now())->where('status', 'present')->count(),

            // LEAVE
            'leave_pending' => Leave::where('status', 'pending')->count(),
            'leave_approved' => Leave::where('status', 'approved')->count(),

            // PAYROLL
            'payroll_total' => Payroll::sum('net_salary'),

            // PERSONAL
            'my_attendance' => Attendance::where('user_id', auth()->id())
                ->whereDate('date', now())
                ->value('status'),

            // EXTRA
            'not_checked_in' => User::whereDoesntHave('attendances', function ($q) {
                $q->whereDate('date', now());
            })->count(),

            'leave_today' => Leave::whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->where('status', 'approved')
                ->count(),
        ];
    }

    #[Computed]
    public function chartData()
    {
        $dates = collect(range(0, 6))
            ->map(fn ($i) => now()->subDays($i)->format('Y-m-d'))
            ->reverse();

        $present = [];
        $absent = [];

        foreach ($dates as $date) {
            $present[] = Attendance::whereDate('date', $date)
                ->where('status', 'present')->count();

            $absent[] = Attendance::whereDate('date', $date)
                ->where('status', 'absent')->count();
        }

        $lateUsers = Attendance::with('user')
            ->where('late_minutes', '>', 0)
            ->selectRaw('user_id, SUM(late_minutes) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'attendance' => [
                'labels' => $dates->values(),
                'present' => $present,
                'absent' => $absent,
            ],
            'leave' => [
                'pending' => Leave::where('status', 'pending')->count(),
                'approved' => Leave::where('status', 'approved')->count(),
                'rejected' => Leave::where('status', 'rejected')->count(),
            ],
            'late' => [
                'labels' => $lateUsers->pluck('user.name'),
                'values' => $lateUsers->pluck('total'),
            ],
        ];
    }

    // ======================
    // REALTIME ACTIVITY
    // ======================
    #[Computed]
    public function realtimeLogs()
    {
        return AttendanceLog::with('user')
            ->latest('recorded_at')
            ->limit(4)
            ->get();
    }

    // ======================
    // WEEKLY CHART
    // ======================
    #[Computed]
    public function weeklyChart()
    {
        $dates = collect(range(0, 6))
            ->map(fn ($i) => now()->subDays($i)->format('Y-m-d'))
            ->reverse()
            ->values();

        $present = [];
        $absent = [];

        foreach ($dates as $date) {
            $present[] = (int) Attendance::whereDate('date', $date)
                ->where('status', 'present')
                ->count();

            $absent[] = (int) Attendance::whereDate('date', $date)
                ->where('status', 'absent')
                ->count();
        }

        return [
            'labels' => $dates->values()->toArray(),
            'present' => array_values($present),
            'absent' => array_values($absent),
        ];
    }

    // ======================
    // MONTHLY CHART
    // ======================
    #[Computed]
    public function monthlyChart()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $days = collect(range(1, $end->day));

        return [
            'labels' => $days,
            'present' => $days->map(fn ($d) => Attendance::whereDate('date', $start->copy()->day($d))
                ->where('status', 'present')->count()
            ),
            'absent' => $days->map(fn ($d) => Attendance::whereDate('date', $start->copy()->day($d))
                ->where('status', 'absent')->count()
            ),
        ];
    }
};
