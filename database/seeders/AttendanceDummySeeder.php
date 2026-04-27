<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceLog;
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
            $endDate = Carbon::create(2026, 4, 26);

            $users = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin');
            })->pluck('id');

            foreach ($users as $userId) {

                $current = $startDate->copy();

                while ($current->lte($endDate)) {

                    $dayOfWeek = $current->dayOfWeekIso;

                    // skip weekend
                    if (in_array($dayOfWeek, [6, 7])) {
                        $current->addDay();

                        continue;
                    }

                    // ===== RANDOM SCENARIO =====
                    $scenario = rand(1, 100);

                    // 1. ABSENT (10%)
                    if ($scenario <= 10) {

                        Attendance::updateOrCreate(
                            [
                                'user_id' => $userId,
                                'date' => $current->toDateString(),
                            ],
                            [
                                'status' => 'absent',
                            ]
                        );

                        $current->addDay();

                        continue;
                    }

                    // 2. HALF DAY / NO CHECKOUT (5%)
                    $noCheckout = $scenario > 10 && $scenario <= 15;

                    // 3. EARLY LEAVE (10%)
                    $earlyLeave = $scenario > 15 && $scenario <= 25;

                    // 4. OVERTIME (15%)
                    $overtimeFlag = $scenario > 25 && $scenario <= 40;

                    // 5. NO BREAK (5%)
                    $noBreak = $scenario > 40 && $scenario <= 45;

                    // 6. MULTIPLE BREAK (5%)
                    $multiBreak = $scenario > 45 && $scenario <= 50;

                    // ===== CHECKIN =====
                    $checkinTime = $current->copy()->setTime(8, rand(0, 30));

                    AttendanceLog::create([
                        'user_id' => $userId,
                        'type' => 'checkin',
                        'latitude' => -3.319437,
                        'longitude' => 114.590752,
                        'recorded_at' => $checkinTime,
                    ]);

                    $totalBreak = 0;

                    if (! $noBreak) {

                        // BREAK 1
                        $breakStart = $current->copy()->setTime(12, rand(0, 10));
                        $breakEnd = $breakStart->copy()->addMinutes(rand(45, 75));

                        AttendanceLog::create([
                            'user_id' => $userId,
                            'type' => 'break_start',
                            'latitude' => -3.319437,
                            'longitude' => 114.590752,
                            'recorded_at' => $breakStart,
                        ]);

                        AttendanceLog::create([
                            'user_id' => $userId,
                            'type' => 'break_end',
                            'latitude' => -3.319437,
                            'longitude' => 114.590752,
                            'recorded_at' => $breakEnd,
                        ]);

                        $totalBreak += $breakStart->diffInMinutes($breakEnd);

                        // BREAK 2 (optional)
                        if ($multiBreak) {
                            $break2Start = $breakEnd->copy()->addMinutes(rand(60, 120));
                            $break2End = $break2Start->copy()->addMinutes(rand(10, 20));

                            AttendanceLog::create([
                                'user_id' => $userId,
                                'type' => 'break_start',
                                'latitude' => -3.319437,
                                'longitude' => 114.590752,
                                'recorded_at' => $break2Start,
                            ]);

                            AttendanceLog::create([
                                'user_id' => $userId,
                                'type' => 'break_end',
                                'latitude' => -3.319437,
                                'longitude' => 114.590752,
                                'recorded_at' => $break2End,
                            ]);

                            $totalBreak += $break2Start->diffInMinutes($break2End);
                        }
                    }

                    // ===== CHECKOUT =====
                    $checkoutTime = null;

                    if (! $noCheckout) {

                        if ($earlyLeave) {
                            $checkoutTime = $current->copy()->setTime(15, rand(0, 30));
                        } elseif ($overtimeFlag) {
                            $checkoutTime = $current->copy()->setTime(18, rand(0, 60));
                        } else {
                            $checkoutTime = $current->copy()->setTime(17, rand(0, 20));
                        }

                        AttendanceLog::create([
                            'user_id' => $userId,
                            'type' => 'checkout',
                            'latitude' => -3.319437,
                            'longitude' => 114.590752,
                            'recorded_at' => $checkoutTime,
                        ]);
                    }

                    // ===== FINAL ATTENDANCE =====
                    $workMinutes = $checkoutTime
                        ? $checkinTime->diffInMinutes($checkoutTime) - $totalBreak
                        : 0;

                    $late = $checkinTime->gt($current->copy()->setTime(8, 0))
                        ? $current->copy()->setTime(8, 0)->diffInMinutes($checkinTime)
                        : 0;

                    $early = ($checkoutTime && $checkoutTime->lt($current->copy()->setTime(17, 0)))
                        ? $checkoutTime->diffInMinutes($current->copy()->setTime(17, 0))
                        : 0;

                    $overtime = ($checkoutTime && $checkoutTime->gt($current->copy()->setTime(17, 0)))
                        ? $current->copy()->setTime(17, 0)->diffInMinutes($checkoutTime)
                        : 0;

                    Attendance::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'date' => $current->toDateString(),
                        ],
                        [
                            'status' => $checkoutTime ? 'present' : 'present',
                            'checkin_at' => $checkinTime,
                            'checkout_at' => $checkoutTime,
                            'work_minutes' => max(0, $workMinutes),
                            'break_minutes' => $totalBreak,
                            'late_minutes' => $late,
                            'early_leave_minutes' => $early,
                            'overtime_minutes' => $overtime,
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
