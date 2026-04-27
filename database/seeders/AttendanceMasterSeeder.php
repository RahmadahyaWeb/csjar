<?php

namespace Database\Seeders;

use App\Models\BreakRule;
use App\Models\EmployeeSchedule;
use App\Models\Shift;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleDay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceMasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            // ========================
            // SHIFTS
            // ========================
            $morning = Shift::create([
                'name' => 'Morning Shift',
                'code' => 'MORNING',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'is_overnight' => false,
                'tolerance_late' => 10,
                'tolerance_early_leave' => 10,
            ]);

            $night = Shift::create([
                'name' => 'Night Shift',
                'code' => 'NIGHT',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'is_overnight' => true,
                'tolerance_late' => 10,
                'tolerance_early_leave' => 10,
            ]);

            // ========================
            // BREAK RULES
            // ========================

            // Morning Shift Breaks
            BreakRule::create([
                'shift_id' => $morning->id,
                'name' => 'Lunch Break',
                'start_time' => '12:00:00',
                'end_time' => '13:00:00',
                'duration_minutes' => 60,
                'is_paid' => false,
                'is_flexible' => false,
            ]);

            // Night Shift Breaks
            BreakRule::create([
                'shift_id' => $night->id,
                'name' => 'Night Break',
                'start_time' => '02:00:00',
                'end_time' => '03:00:00',
                'duration_minutes' => 60,
                'is_paid' => false,
                'is_flexible' => false,
            ]);

            // ========================
            // WORK SCHEDULE
            // ========================
            $office = WorkSchedule::create([
                'name' => 'Office Schedule',
                'code' => 'OFFICE',
            ]);

            // ========================
            // WORK SCHEDULE DAYS
            // ========================
            foreach (range(0, 6) as $day) {
                WorkScheduleDay::create([
                    'work_schedule_id' => $office->id,
                    'day_of_week' => $day,
                    'shift_id' => in_array($day, [0, 6]) ? null : $morning->id,
                    'is_working_day' => ! in_array($day, [0, 6]),
                ]);
            }

            // ========================
            // EMPLOYEE SCHEDULE
            // ========================
            $users = User::pluck('id');

            foreach ($users as $userId) {
                EmployeeSchedule::create([
                    'user_id' => $userId,
                    'work_schedule_id' => $office->id,
                    'start_date' => now(),
                    'is_active' => true,
                ]);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
