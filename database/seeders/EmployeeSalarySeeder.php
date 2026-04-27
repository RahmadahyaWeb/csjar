<?php

// database/seeders/EmployeeSalarySeeder.php

namespace Database\Seeders;

use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSalarySeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            $users = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin');
            })->get();

            foreach ($users as $user) {

                // basic salary berdasarkan pola sederhana (biar variatif)
                $base = match (true) {
                    str_contains($user->email, 'manager') => 8000000,
                    str_contains($user->email, 'hr') => 7000000,
                    str_contains($user->email, 'finance') => 6500000,
                    str_contains($user->email, 'it') => 7000000,
                    default => 5000000,
                };

                EmployeeSalary::updateOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    [
                        'basic_salary' => $base,
                        'effective_date' => now()->startOfYear(),
                    ]
                );
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
