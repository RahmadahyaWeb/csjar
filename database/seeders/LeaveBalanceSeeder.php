<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            $year = now()->year;

            $users = User::pluck('id');

            foreach ($users as $userId) {

                // ANNUAL LEAVE
                LeaveBalance::create([
                    'user_id' => $userId,
                    'type' => 'annual',
                    'year' => $year,
                    'quota' => 12,
                    'used' => 0,
                    'remaining' => 12,
                ]);

                // SICK LEAVE
                LeaveBalance::create([
                    'user_id' => $userId,
                    'type' => 'sick',
                    'year' => $year,
                    'quota' => 12,
                    'used' => 0,
                    'remaining' => 12,
                ]);

                // PERMIT / IZIN
                LeaveBalance::create([
                    'user_id' => $userId,
                    'type' => 'permit',
                    'year' => $year,
                    'quota' => 6,
                    'used' => 0,
                    'remaining' => 6,
                ]);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
