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
            // SHIFT (ONLY MORNING)
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

            // ========================
            // BREAK RULES
            // ========================
            BreakRule::create([
                'shift_id' => $morning->id,
                'name' => 'Lunch Break',
                'start_time' => '12:00:00',
                'end_time' => '13:00:00',
                'duration_minutes' => 60,
                'is_paid' => false,
                'is_flexible' => false,
            ]);

            // ========================
            // WORK SCHEDULE (2 TYPES)
            // ========================
            $office5Days = WorkSchedule::create([
                'name' => 'Office 5 Days',
                'code' => 'OFFICE-5',
            ]);

            $office6Days = WorkSchedule::create([
                'name' => 'Office 6 Days',
                'code' => 'OFFICE-6',
            ]);

            // ========================
            // WORK SCHEDULE DAYS (5 DAYS: MON-FRI)
            // ========================
            foreach (range(1, 7) as $day) {
                WorkScheduleDay::create([
                    'work_schedule_id' => $office5Days->id,
                    'day_of_week' => $day,
                    'shift_id' => in_array($day, [1, 2, 3, 4, 5]) ? $morning->id : null,
                    'is_working_day' => in_array($day, [1, 2, 3, 4, 5]),
                ]);
            }

            // ========================
            // WORK SCHEDULE DAYS (6 DAYS: MON-SAT)
            // ========================
            foreach (range(1, 7) as $day) {
                WorkScheduleDay::create([
                    'work_schedule_id' => $office6Days->id,
                    'day_of_week' => $day,
                    'shift_id' => in_array($day, [1, 2, 3, 4, 5, 6]) ? $morning->id : null,
                    'is_working_day' => in_array($day, [1, 2, 3, 4, 5, 6]),
                ]);
            }

            // ========================
            // EMPLOYEE SCHEDULE
            // ========================
            $users = User::pluck('id');

            foreach ($users as $index => $userId) {

                // Alternating assignment (biar ada variasi)
                $scheduleId = $index % 2 === 0
                    ? $office5Days->id
                    : $office6Days->id;

                EmployeeSchedule::create([
                    'user_id' => $userId,
                    'work_schedule_id' => $scheduleId,
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
