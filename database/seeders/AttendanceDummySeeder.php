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
            $endDate = Carbon::create(2026, 4, 7);

            $users = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin');
            })
                ->limit(2)
                ->pluck('id');

            foreach ($users as $userId) {

                $current = $startDate->copy();

                while ($current->lte($endDate)) {

                    if (in_array($current->dayOfWeekIso, [6, 7])) {
                        $current->addDay();

                        continue;
                    }

                    // ===== SCENARIO =====
                    $scenario = match ($current->day % 7) {
                        0 => 'absent',
                        1 => 'normal',
                        2 => 'late',
                        3 => 'early',
                        4 => 'overtime',
                        5 => 'no_break',
                        6 => 'multi_break',
                    };

                    $lat = -3.319437;
                    $lng = 114.590752;

                    // ===== ABSENT =====
                    if ($scenario === 'absent') {

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

                    $baseStart = $current->copy()->setTime(8, 0);
                    $baseEnd = $current->copy()->setTime(17, 0);

                    // ===== CHECKIN =====
                    $checkinTime = match ($scenario) {
                        'late' => $baseStart->copy()->addMinutes(rand(15, 45)),
                        default => $baseStart->copy()->addMinutes(rand(0, 10)),
                    };

                    AttendanceLog::create([
                        'user_id' => $userId,
                        'type' => 'checkin',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'recorded_at' => $checkinTime,
                    ]);

                    $totalBreak = 0;

                    // ===== BREAK =====
                    if ($scenario !== 'no_break') {

                        $breakStart = $current->copy()->setTime(12, rand(0, 15));
                        $breakEnd = $breakStart->copy()->addMinutes(rand(30, 90));

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

                        $totalBreak += $breakStart->diffInMinutes($breakEnd);

                        if ($scenario === 'multi_break') {

                            $break2Start = $breakEnd->copy()->addMinutes(rand(60, 120));
                            $break2End = $break2Start->copy()->addMinutes(rand(10, 25));

                            AttendanceLog::insert([
                                [
                                    'user_id' => $userId,
                                    'type' => 'break_start',
                                    'latitude' => $lat,
                                    'longitude' => $lng,
                                    'recorded_at' => $break2Start,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ],
                                [
                                    'user_id' => $userId,
                                    'type' => 'break_end',
                                    'latitude' => $lat,
                                    'longitude' => $lng,
                                    'recorded_at' => $break2End,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ],
                            ]);

                            $totalBreak += $break2Start->diffInMinutes($break2End);
                        }
                    }

                    // ===== CHECKOUT =====
                    $checkoutTime = match ($scenario) {
                        'early' => $baseEnd->copy()->subMinutes(rand(60, 180)),
                        'overtime' => $baseEnd->copy()->addMinutes(rand(30, 120)),
                        default => $baseEnd->copy()->addMinutes(rand(0, 15)),
                    };

                    AttendanceLog::create([
                        'user_id' => $userId,
                        'type' => 'checkout',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'recorded_at' => $checkoutTime,
                    ]);

                    // ===== CALCULATION =====
                    $workMinutes = max(0,
                        $checkinTime->diffInMinutes($checkoutTime) - $totalBreak
                    );

                    $late = $checkinTime->gt($baseStart)
                        ? $baseStart->diffInMinutes($checkinTime)
                        : 0;

                    $early = $checkoutTime->lt($baseEnd)
                        ? $checkoutTime->diffInMinutes($baseEnd)
                        : 0;

                    $overtime = $checkoutTime->gt($baseEnd)
                        ? $baseEnd->diffInMinutes($checkoutTime)
                        : 0;

                    // ===== NEW FIELD (OVERTIME APPROVAL) =====
                    $isApproved = $scenario === 'overtime'
                        ? (bool) rand(0, 1) // random approve / reject
                        : false;

                    Attendance::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'date' => $current->toDateString(),
                        ],
                        [
                            'status' => 'present',
                            'checkin_at' => $checkinTime,
                            'checkout_at' => $checkoutTime,
                            'work_minutes' => $workMinutes,
                            'break_minutes' => $totalBreak,
                            'late_minutes' => $late,
                            'early_leave_minutes' => $early,
                            'overtime_minutes' => $overtime,
                            'is_overtime_approved' => $isApproved,
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
