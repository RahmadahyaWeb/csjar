<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\BreakRule;
use App\Models\Department;
use App\Models\EmployeeAssignment;
use App\Models\EmployeeSchedule;
use App\Models\Position;
use App\Models\Shift;
use App\Models\Team;
use App\Models\User;
use App\Models\UserFace;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleDay;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $service;

    protected $descriptor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AttendanceService::class);
        $this->descriptor = array_fill(0, 128, 0.5);

        // USER
        $this->user = User::factory()->create();

        UserFace::create([
            'user_id' => $this->user->id,
            'descriptor' => $this->descriptor,
        ]);

        // BRANCH
        $branch = Branch::create([
            'name' => 'HO',
            'code' => 'HO',
            'latitude' => -3.319437,
            'longitude' => 114.590752,
            'radius' => 100,
        ]);

        $department = Department::create([
            'branch_id' => $branch->id,
            'name' => 'IT',
            'code' => 'IT',
        ]);

        $position = Position::create([
            'department_id' => $department->id,
            'name' => 'Staff',
            'code' => 'STF',
            'level' => 1,
        ]);

        $team = Team::create([
            'department_id' => $department->id,
            'name' => 'Team',
            'code' => 'T1',
        ]);

        EmployeeAssignment::create([
            'user_id' => $this->user->id,
            'branch_id' => $branch->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'team_id' => $team->id,
            'start_date' => '2026-01-01',
            'is_active' => true,
        ]);

        // SHIFT
        $shift = Shift::create([
            'name' => 'Morning',
            'code' => 'MOR',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'is_overnight' => false,
            'tolerance_late' => 10,
        ]);

        BreakRule::create([
            'shift_id' => $shift->id,
            'name' => 'Lunch',
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'duration_minutes' => 60,
        ]);

        // SCHEDULE (ALL WORKING DAY)
        $schedule = WorkSchedule::create([
            'name' => 'Office',
            'code' => 'OFF',
        ]);

        foreach (range(1, 7) as $day) {
            WorkScheduleDay::create([
                'work_schedule_id' => $schedule->id,
                'day_of_week' => $day,
                'shift_id' => $shift->id,
                'is_working_day' => true,
            ]);
        }

        EmployeeSchedule::create([
            'user_id' => $this->user->id,
            'work_schedule_id' => $schedule->id,
            'start_date' => '2026-01-01',
            'is_active' => true,
        ]);
    }

    // ======================
    // BREAK FLOW
    // ======================
    public function test_user_can_start_and_end_break()
    {
        Carbon::setTestNow('2026-04-01 08:05:00');

        $this->service->checkIn($this->user->id, -3.319437, 114.590752, $this->descriptor);

        Carbon::setTestNow('2026-04-01 12:05:00');

        $this->service->startBreak($this->user->id, -3.319437, 114.590752);

        $this->assertDatabaseHas('attendance_logs', [
            'type' => 'break_start',
        ]);

        Carbon::setTestNow('2026-04-01 12:45:00');

        $this->service->endBreak($this->user->id, -3.319437, 114.590752);

        $this->assertDatabaseHas('attendance_logs', [
            'type' => 'break_end',
        ]);
    }

    // ======================
    // BREAK INVALID TIME
    // ======================
    public function test_break_outside_rule_should_fail()
    {
        Carbon::setTestNow('2026-04-01 08:05:00');

        $this->service->checkIn($this->user->id, -3.319437, 114.590752, $this->descriptor);

        Carbon::setTestNow('2026-04-01 10:00:00');

        $this->expectException(\Exception::class);

        $this->service->startBreak($this->user->id, -3.319437, 114.590752);
    }

    // ======================
    // GPS VALIDATION
    // ======================
    public function test_outside_radius_should_fail()
    {
        Carbon::setTestNow('2026-04-01 08:05:00');

        $this->expectException(\Exception::class);

        $this->service->checkIn(
            $this->user->id,
            -3.0, // jauh
            114.0,
            $this->descriptor
        );
    }

    // ======================
    // WORKING DAY VALIDATION
    // ======================
    public function test_non_working_day_should_fail()
    {
        // override jadi tidak working
        WorkScheduleDay::query()->update([
            'is_working_day' => false,
        ]);

        Carbon::setTestNow('2026-04-01 08:05:00');

        $this->expectException(\Exception::class);

        $this->service->checkIn(
            $this->user->id,
            -3.319437,
            114.590752,
            $this->descriptor
        );
    }

    // ======================
    // PROCESS DAILY RESULT
    // ======================
    public function test_process_daily_generates_attendance()
    {
        Carbon::setTestNow('2026-04-01 08:05:00');

        $this->service->checkIn($this->user->id, -3.319437, 114.590752, $this->descriptor);

        Carbon::setTestNow('2026-04-01 12:00:00');

        $this->service->startBreak($this->user->id, -3.319437, 114.590752);

        Carbon::setTestNow('2026-04-01 13:00:00');

        $this->service->endBreak($this->user->id, -3.319437, 114.590752);

        Carbon::setTestNow('2026-04-01 16:50:00');

        $this->service->checkOut($this->user->id, -3.319437, 114.590752, $this->descriptor);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'status' => 'present',
        ]);

        $attendance = Attendance::where('user_id', $this->user->id)->first();

        $this->assertGreaterThan(0, $attendance->work_minutes);
        $this->assertGreaterThan(0, $attendance->break_minutes);
    }

    // ======================
    // FACE STRICT VALIDATION
    // ======================
    public function test_face_descriptor_must_be_128_length()
    {
        Carbon::setTestNow('2026-04-01 08:05:00');

        $this->expectException(\Exception::class);

        $this->service->checkIn(
            $this->user->id,
            -3.319437,
            114.590752,
            [1, 2, 3] // invalid
        );
    }
}
