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

            $users = User::with('employeeAssignment.position')
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'super_admin');
                })
                ->get();

            foreach ($users as $user) {

                $position = strtolower(
                    optional($user->employeeAssignment?->position)->name ?? ''
                );

                $basicSalary = match (true) {

                    // ========================
                    // EXECUTIVE
                    // ========================
                    str_contains($position, 'direktur') => 25000000,
                    str_contains($position, 'manajer umum') => 18000000,

                    // ========================
                    // HEAD / MANAGER
                    // ========================
                    str_contains($position, 'pemimpin redaksi') => 15000000,
                    str_contains($position, 'produser eksekutif') => 14000000,
                    str_contains($position, 'kepala digital') => 12000000,
                    str_contains($position, 'kepala pemasaran') => 12000000,
                    str_contains($position, 'kepala operasional') => 13000000,
                    str_contains($position, 'kepala hrd') => 12000000,

                    // ========================
                    // SPECIALIST / EDITOR
                    // ========================
                    str_contains($position, 'redaktur') => 8500000,
                    str_contains($position, 'spesialis') => 7500000,

                    // ========================
                    // CREATIVE
                    // ========================
                    str_contains($position, 'motion') => 7000000,
                    str_contains($position, 'desainer') => 6500000,
                    str_contains($position, 'produksi video') => 6500000,

                    // ========================
                    // OPERATIONAL
                    // ========================
                    str_contains($position, 'dukungan ti') => 7000000,
                    str_contains($position, 'staf hrd') => 6000000,

                    // ========================
                    // REPORTER
                    // ========================
                    str_contains($position, 'reporter') => 5500000,

                    default => 5000000,
                };

                EmployeeSalary::updateOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    [
                        'basic_salary' => $basicSalary,
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
