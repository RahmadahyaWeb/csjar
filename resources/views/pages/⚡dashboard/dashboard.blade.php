{{-- resources/views/livewire/pages/dashboard/index.blade.php --}}

{{-- resources/views/livewire/pages/dashboard/index.blade.php --}}

<div x-data x-init="setTimeout(() => {
    renderDashboardCharts(@js([
    'attendance' => $this->chartData['attendance'],
    'leave' => $this->chartData['leave'],
    'late' => $this->chartData['late'],
    'weekly' => $this->weeklyChart,
    'monthly' => $this->monthlyChart,
]))
}, 100)">

    <x-page-header title="Dashboard" description="Overview system activity" />

    {{-- ===================== --}}
    {{-- GLOBAL / ADMIN / HR --}}
    {{-- ===================== --}}
    @can('attendance-monitoring.view')
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">

            {{-- ATTENDANCE --}}
            <flux:card class="bg-blue-50 border border-blue-100">
                <div class="text-sm text-blue-600">Attendance Today</div>
                <div class="text-xl font-semibold text-blue-700">
                    {{ $this->stats['attendance_today'] }}
                </div>
            </flux:card>

            {{-- PRESENT --}}
            <flux:card class="bg-green-50 border border-green-100">
                <div class="text-sm text-green-600">Present Today</div>
                <div class="text-xl font-semibold text-green-700">
                    {{ $this->stats['present_today'] }}
                </div>
            </flux:card>

            {{-- NOT CHECKIN --}}
            <flux:card class="bg-red-50 border border-red-100">
                <div class="text-sm text-red-600">Not Check-in</div>
                <div class="text-xl font-semibold text-red-700">
                    {{ $this->stats['not_checked_in'] }}
                </div>
            </flux:card>

            {{-- LEAVE --}}
            <flux:card class="bg-yellow-50 border border-yellow-100">
                <div class="text-sm text-yellow-600">On Leave</div>
                <div class="text-xl font-semibold text-yellow-700">
                    {{ $this->stats['leave_today'] }}
                </div>
            </flux:card>

            {{-- PAYROLL --}}
            <flux:card class="bg-purple-50 border border-purple-100">
                <div class="text-sm text-purple-600">Total Payroll</div>
                <div class="text-xl font-semibold text-purple-700">
                    {{ number_format($this->stats['payroll_total']) }}
                </div>
            </flux:card>

        </div>
    @endcan

    {{-- ===================== --}}
    {{-- REALTIME ACTIVITY --}}
    {{-- ===================== --}}
    @can('attendance-monitoring.view')
        <flux:card class="mb-4">
            <div class="mb-2 font-medium">Realtime Attendance Activity</div>

            <div class="space-y-2 text-sm">
                @forelse ($this->realtimeLogs as $log)
                    <div class="flex justify-between border-b pb-1">
                        <div>
                            {{ $log->user->name }}
                            <span class="text-xs text-zinc-500">
                                ({{ strtoupper($log->type) }})
                            </span>
                        </div>
                        <div class="text-xs text-zinc-500">
                            {{ $log->recorded_at }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-zinc-500">No activity</div>
                @endforelse
            </div>
        </flux:card>
    @endcan

    {{-- ===================== --}}
    {{-- CHARTS (HR / ADMIN) --}}
    {{-- ===================== --}}
    @can('attendance-report.view')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

            <flux:card>
                <div class="mb-2 font-medium">Attendance Trend (7 Days)</div>
                <div id="attendanceChart"></div>
            </flux:card>

            <flux:card>
                <div class="mb-2 font-medium">Leave Overview</div>
                <div id="leaveChart"></div>
            </flux:card>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

            <flux:card>
                <div class="mb-2 font-medium">Weekly Attendance</div>
                <div id="weeklyChart"></div>
            </flux:card>

            <flux:card>
                <div class="mb-2 font-medium">Monthly Attendance</div>
                <div id="monthlyChart"></div>
            </flux:card>

        </div>

        <flux:card class="mb-4">
            <div class="mb-2 font-medium">Top Late Employees</div>
            <div id="lateChart"></div>
        </flux:card>
    @endcan

    {{-- ===================== --}}
    {{-- HEAD (APPROVAL) --}}
    {{-- ===================== --}}
    @can('leave.approve')
        <flux:card class="mb-4">
            <div class="mb-2 font-medium">Approval Summary</div>

            <div class="grid grid-cols-2 gap-3">
                <div>Pending Leave: {{ $this->stats['leave_pending'] }}</div>
                <div>Approved Leave: {{ $this->stats['leave_approved'] }}</div>
            </div>
        </flux:card>
    @endcan

    {{-- ===================== --}}
    {{-- EMPLOYEE (PERSONAL) --}}
    {{-- ===================== --}}
    @can('my-attendance.view')
        <flux:card>
            <div class="mb-2 font-medium">My Today Status</div>

            <div class="text-xl font-semibold">
                {{ strtoupper($this->stats['my_attendance'] ?? 'NO DATA') }}
            </div>
        </flux:card>
    @endcan

</div>
