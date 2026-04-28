<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\EmployeeAssignment;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            // ========================
            // ROLES
            // ========================
            $roleHead = Role::where('name', 'head')->first();
            $roleEmployee = Role::where('name', 'employee')->first();
            $roleHr = Role::where('name', 'hr')->first();
            $roleHrStaff = Role::where('name', 'hr_staff')->first();

            // ========================
            // BRANCH
            // ========================
            $branch = Branch::create([
                'name' => 'Head Office',
                'code' => 'HO',
                'latitude' => -3.319437,
                'longitude' => 114.590752,
                'radius' => 100,
            ]);

            // ========================
            // USERS (1 HEAD + 1 STAFF PER DEPT)
            // ========================

            // FINANCE
            $financeHead = User::create([
                'name' => 'Budi Santoso',
                'email' => 'budi.finance@mail.com',
                'password' => Hash::make('password'),
            ]);
            $financeHead->syncRoles([$roleHead]);

            $financeStaff = User::create([
                'name' => 'Rina Kurniawati',
                'email' => 'rina.finance@mail.com',
                'password' => Hash::make('password'),
            ]);
            $financeStaff->syncRoles([$roleEmployee]);

            // IT
            $itHead = User::create([
                'name' => 'Dedi Saputra',
                'email' => 'dedi.it@mail.com',
                'password' => Hash::make('password'),
            ]);
            $itHead->syncRoles([$roleHead]);

            $itStaff = User::create([
                'name' => 'Rizky Hidayat',
                'email' => 'rizky.it@mail.com',
                'password' => Hash::make('password'),
            ]);
            $itStaff->syncRoles([$roleEmployee]);

            // HR
            $hrHead = User::create([
                'name' => 'Siti Rahmawati',
                'email' => 'siti.hr@mail.com',
                'password' => Hash::make('password'),
            ]);
            $hrHead->syncRoles([$roleHr]);

            $hrStaff = User::create([
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.hr@mail.com',
                'password' => Hash::make('password'),
            ]);
            $hrStaff->syncRoles([$roleHrStaff]);

            // ========================
            // DEPARTMENTS
            // ========================
            $finance = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Finance',
                'code' => 'FIN',
                'head_user_id' => $financeHead->id,
            ]);

            $it = Department::create([
                'branch_id' => $branch->id,
                'name' => 'IT',
                'code' => 'IT',
                'head_user_id' => $itHead->id,
            ]);

            $hr = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Human Resource',
                'code' => 'HR',
                'head_user_id' => $hrHead->id,
            ]);

            // ========================
            // POSITIONS
            // ========================
            $financeHeadPos = Position::create([
                'department_id' => $finance->id,
                'name' => 'Finance Head',
                'code' => 'FIN-H',
                'level' => 1,
                'head_user_id' => $financeHead->id,
            ]);

            $financeStaffPos = Position::create([
                'department_id' => $finance->id,
                'name' => 'Finance Staff',
                'code' => 'FIN-S',
                'level' => 2,
                'parent_id' => $financeHeadPos->id,
            ]);

            $itHeadPos = Position::create([
                'department_id' => $it->id,
                'name' => 'IT Head',
                'code' => 'IT-H',
                'level' => 1,
                'head_user_id' => $itHead->id,
            ]);

            $itStaffPos = Position::create([
                'department_id' => $it->id,
                'name' => 'IT Staff',
                'code' => 'IT-S',
                'level' => 2,
                'parent_id' => $itHeadPos->id,
            ]);

            $hrHeadPos = Position::create([
                'department_id' => $hr->id,
                'name' => 'HR Head',
                'code' => 'HR-H',
                'level' => 1,
                'head_user_id' => $hrHead->id,
            ]);

            $hrStaffPos = Position::create([
                'department_id' => $hr->id,
                'name' => 'HR Staff',
                'code' => 'HR-S',
                'level' => 2,
                'parent_id' => $hrHeadPos->id,
            ]);

            // ========================
            // TEAMS
            // ========================
            $financeTeam = Team::create([
                'department_id' => $finance->id,
                'name' => 'Finance Team',
                'code' => 'FIN-T',
                'lead_user_id' => $financeHead->id,
            ]);

            $itTeam = Team::create([
                'department_id' => $it->id,
                'name' => 'IT Team',
                'code' => 'IT-T',
                'lead_user_id' => $itHead->id,
            ]);

            $hrTeam = Team::create([
                'department_id' => $hr->id,
                'name' => 'HR Team',
                'code' => 'HR-T',
                'lead_user_id' => $hrHead->id,
            ]);

            // ========================
            // ASSIGNMENTS
            // ========================
            EmployeeAssignment::insert([
                // FINANCE
                [
                    'user_id' => $financeHead->id,
                    'branch_id' => $branch->id,
                    'department_id' => $finance->id,
                    'position_id' => $financeHeadPos->id,
                    'team_id' => $financeTeam->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $financeStaff->id,
                    'branch_id' => $branch->id,
                    'department_id' => $finance->id,
                    'position_id' => $financeStaffPos->id,
                    'team_id' => $financeTeam->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // IT
                [
                    'user_id' => $itHead->id,
                    'branch_id' => $branch->id,
                    'department_id' => $it->id,
                    'position_id' => $itHeadPos->id,
                    'team_id' => $itTeam->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $itStaff->id,
                    'branch_id' => $branch->id,
                    'department_id' => $it->id,
                    'position_id' => $itStaffPos->id,
                    'team_id' => $itTeam->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // HR
                [
                    'user_id' => $hrHead->id,
                    'branch_id' => $branch->id,
                    'department_id' => $hr->id,
                    'position_id' => $hrHeadPos->id,
                    'team_id' => $hrTeam->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $hrStaff->id,
                    'branch_id' => $branch->id,
                    'department_id' => $hr->id,
                    'position_id' => $hrStaffPos->id,
                    'team_id' => $hrTeam->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
