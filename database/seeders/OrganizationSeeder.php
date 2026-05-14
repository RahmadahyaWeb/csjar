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

            // =========================================================
            // USERS
            // =========================================================

            // ========================
            // DIREKSI
            // ========================
            $director = User::create([
                'name' => 'Rahmat Hidayatullah',
                'email' => 'rahmat@mail.com',
                'password' => Hash::make('password'),
            ]);
            $director->syncRoles([$roleHead]);

            $generalManager = User::create([
                'name' => 'Ahdalena',
                'email' => 'ahdalena@mail.com',
                'password' => Hash::make('password'),
            ]);
            $generalManager->syncRoles([$roleHead]);

            // ========================
            // REDAKSI
            // ========================
            $editorChief = User::create([
                'name' => 'Zepi Al Ayubi',
                'email' => 'zepi@mail.com',
                'password' => Hash::make('password'),
            ]);
            $editorChief->syncRoles([$roleHead]);

            $editor1 = User::create([
                'name' => 'Ari Sukma Setiawan',
                'email' => 'ari@mail.com',
                'password' => Hash::make('password'),
            ]);
            $editor1->syncRoles([$roleEmployee]);

            $editor2 = User::create([
                'name' => 'Nina Megasari',
                'email' => 'nina@mail.com',
                'password' => Hash::make('password'),
            ]);
            $editor2->syncRoles([$roleEmployee]);

            $reporters = collect([
                'Wanda Nurazizah',
                'Lana Kelana',
                'Akbar Rizaldi',
                'Sofyan Suri',
                'Basuki Rahmat',
                'Ahmad Maulana',
                'Ghina Laudza',
            ])->map(function ($name, $index) use ($roleEmployee) {

                $user = User::create([
                    'name' => $name,
                    'email' => 'reporter'.$index.'@mail.com',
                    'password' => Hash::make('password'),
                ]);

                $user->syncRoles([$roleEmployee]);

                return $user;
            });

            // ========================
            // KREATIF
            // ========================
            $creativeHead = User::create([
                'name' => 'Musa Bastara',
                'email' => 'musa@mail.com',
                'password' => Hash::make('password'),
            ]);
            $creativeHead->syncRoles([$roleHead]);

            $creativeUsers = collect([
                'Muhammad Rosyid',
                'Fajar Shadiq Redhani',
                'Feldi',
                'Muhammad Arya',
                'Muhammad Nahli',
            ])->map(function ($name, $index) use ($roleEmployee) {

                $user = User::create([
                    'name' => $name,
                    'email' => 'creative'.$index.'@mail.com',
                    'password' => Hash::make('password'),
                ]);

                $user->syncRoles([$roleEmployee]);

                return $user;
            });

            // ========================
            // DIGITAL
            // ========================
            $digitalHead = User::create([
                'name' => 'Aisya Fathika Sari',
                'email' => 'aisya@mail.com',
                'password' => Hash::make('password'),
            ]);
            $digitalHead->syncRoles([$roleHead]);

            $digitalStaff = User::create([
                'name' => 'Fadia Aulia',
                'email' => 'fadia@mail.com',
                'password' => Hash::make('password'),
            ]);
            $digitalStaff->syncRoles([$roleEmployee]);

            // ========================
            // MARKETING
            // ========================
            $marketingHead = User::create([
                'name' => 'Putri Nadya Oktariana',
                'email' => 'putri@mail.com',
                'password' => Hash::make('password'),
            ]);
            $marketingHead->syncRoles([$roleHead]);

            $marketingStaff = User::create([
                'name' => 'Siti Maisyarah',
                'email' => 'siti.marketing@mail.com',
                'password' => Hash::make('password'),
            ]);
            $marketingStaff->syncRoles([$roleEmployee]);

            // ========================
            // HR & OPERATIONAL
            // ========================
            $operationalHead = User::create([
                'name' => 'Andy Arfian',
                'email' => 'andy@mail.com',
                'password' => Hash::make('password'),
            ]);
            $operationalHead->syncRoles([$roleHead]);

            $hrHead = User::create([
                'name' => 'Ilfan Nor Ridhoni',
                'email' => 'ilfan@mail.com',
                'password' => Hash::make('password'),
            ]);
            $hrHead->syncRoles([$roleHr]);

            $hrStaff1 = User::create([
                'name' => 'Muhammad Amrullah',
                'email' => 'amrullah@mail.com',
                'password' => Hash::make('password'),
            ]);
            $hrStaff1->syncRoles([$roleHrStaff]);

            $hrStaff2 = User::create([
                'name' => 'Annisa Dwi Oktaviarini',
                'email' => 'annisa@mail.com',
                'password' => Hash::make('password'),
            ]);
            $hrStaff2->syncRoles([$roleHrStaff]);

            $itSupport = User::create([
                'name' => 'Moh. Iqbal Alghifari',
                'email' => 'iqbal@mail.com',
                'password' => Hash::make('password'),
            ]);
            $itSupport->syncRoles([$roleEmployee]);

            // =========================================================
            // DEPARTMENTS
            // =========================================================

            $executive = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Direksi',
                'code' => 'EXEC',
                'head_user_id' => $director->id,
            ]);

            $editorial = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Redaksi',
                'code' => 'EDIT',
                'head_user_id' => $editorChief->id,
            ]);

            $creative = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Kreatif & Produksi',
                'code' => 'CREATIVE',
                'head_user_id' => $creativeHead->id,
            ]);

            $digital = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Digital',
                'code' => 'DIGITAL',
                'head_user_id' => $digitalHead->id,
            ]);

            $marketing = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Marketing',
                'code' => 'MKT',
                'head_user_id' => $marketingHead->id,
            ]);

            $hr = Department::create([
                'branch_id' => $branch->id,
                'name' => 'Human Resource',
                'code' => 'HR',
                'head_user_id' => $hrHead->id,
            ]);

            // =========================================================
            // POSITIONS
            // =========================================================

            $positions = [];

            $positions['director'] = Position::create([
                'department_id' => $executive->id,
                'name' => 'Direktur Utama',
                'code' => 'DIR',
                'level' => 1,
                'head_user_id' => $director->id,
            ]);

            $positions['gm'] = Position::create([
                'department_id' => $executive->id,
                'name' => 'Manajer Umum',
                'code' => 'GM',
                'level' => 2,
                'parent_id' => $positions['director']->id,
            ]);

            $positions['editor_chief'] = Position::create([
                'department_id' => $editorial->id,
                'name' => 'Pemimpin Redaksi',
                'code' => 'EDITOR-HEAD',
                'level' => 1,
                'head_user_id' => $editorChief->id,
            ]);

            $positions['editor'] = Position::create([
                'department_id' => $editorial->id,
                'name' => 'Redaktur',
                'code' => 'EDITOR',
                'level' => 2,
                'parent_id' => $positions['editor_chief']->id,
            ]);

            $positions['reporter'] = Position::create([
                'department_id' => $editorial->id,
                'name' => 'Reporter',
                'code' => 'REPORTER',
                'level' => 3,
                'parent_id' => $positions['editor']->id,
            ]);

            $positions['creative_head'] = Position::create([
                'department_id' => $creative->id,
                'name' => 'Produser Eksekutif',
                'code' => 'PROD-HEAD',
                'level' => 1,
                'head_user_id' => $creativeHead->id,
            ]);

            $positions['video_staff'] = Position::create([
                'department_id' => $creative->id,
                'name' => 'Staf Produksi Video',
                'code' => 'VIDEO',
                'level' => 2,
                'parent_id' => $positions['creative_head']->id,
            ]);

            $positions['designer'] = Position::create([
                'department_id' => $creative->id,
                'name' => 'Desainer Grafis',
                'code' => 'DESIGN',
                'level' => 2,
                'parent_id' => $positions['creative_head']->id,
            ]);

            $positions['motion'] = Position::create([
                'department_id' => $creative->id,
                'name' => 'Desainer Motion Graphic',
                'code' => 'MOTION',
                'level' => 2,
                'parent_id' => $positions['creative_head']->id,
            ]);

            $positions['digital_head'] = Position::create([
                'department_id' => $digital->id,
                'name' => 'Kepala Digital',
                'code' => 'DIGITAL-HEAD',
                'level' => 1,
                'head_user_id' => $digitalHead->id,
            ]);

            $positions['social_media'] = Position::create([
                'department_id' => $digital->id,
                'name' => 'Spesialis Media Sosial',
                'code' => 'SOCMED',
                'level' => 2,
                'parent_id' => $positions['digital_head']->id,
            ]);

            $positions['marketing_head'] = Position::create([
                'department_id' => $marketing->id,
                'name' => 'Kepala Pemasaran',
                'code' => 'MKT-HEAD',
                'level' => 1,
                'head_user_id' => $marketingHead->id,
            ]);

            $positions['marketing_staff'] = Position::create([
                'department_id' => $marketing->id,
                'name' => 'Spesialis Pemasaran',
                'code' => 'MKT-STAFF',
                'level' => 2,
                'parent_id' => $positions['marketing_head']->id,
            ]);

            $positions['operational_head'] = Position::create([
                'department_id' => $hr->id,
                'name' => 'Kepala Operasional',
                'code' => 'OPS-HEAD',
                'level' => 1,
                'head_user_id' => $operationalHead->id,
            ]);

            $positions['hr_head'] = Position::create([
                'department_id' => $hr->id,
                'name' => 'Kepala HRD & Administrasi',
                'code' => 'HR-HEAD',
                'level' => 2,
                'head_user_id' => $hrHead->id,
                'parent_id' => $positions['operational_head']->id,
            ]);

            $positions['hr_staff'] = Position::create([
                'department_id' => $hr->id,
                'name' => 'Staf HRD & Administrasi',
                'code' => 'HR-STAFF',
                'level' => 3,
                'parent_id' => $positions['hr_head']->id,
            ]);

            $positions['it_support'] = Position::create([
                'department_id' => $hr->id,
                'name' => 'Dukungan TI',
                'code' => 'IT-SUPPORT',
                'level' => 3,
                'parent_id' => $positions['operational_head']->id,
            ]);

            // =========================================================
            // TEAMS
            // =========================================================

            $teams = [
                'executive' => Team::create([
                    'department_id' => $executive->id,
                    'name' => 'Executive Team',
                    'code' => 'EXEC-T',
                    'lead_user_id' => $director->id,
                ]),

                'editorial' => Team::create([
                    'department_id' => $editorial->id,
                    'name' => 'Editorial Team',
                    'code' => 'EDIT-T',
                    'lead_user_id' => $editorChief->id,
                ]),

                'creative' => Team::create([
                    'department_id' => $creative->id,
                    'name' => 'Creative Team',
                    'code' => 'CREATIVE-T',
                    'lead_user_id' => $creativeHead->id,
                ]),

                'digital' => Team::create([
                    'department_id' => $digital->id,
                    'name' => 'Digital Team',
                    'code' => 'DIGITAL-T',
                    'lead_user_id' => $digitalHead->id,
                ]),

                'marketing' => Team::create([
                    'department_id' => $marketing->id,
                    'name' => 'Marketing Team',
                    'code' => 'MKT-T',
                    'lead_user_id' => $marketingHead->id,
                ]),

                'hr' => Team::create([
                    'department_id' => $hr->id,
                    'name' => 'HR Team',
                    'code' => 'HR-T',
                    'lead_user_id' => $hrHead->id,
                ]),
            ];

            // =========================================================
            // ASSIGNMENT HELPER
            // =========================================================

            $assignments = [];

            $pushAssignment = function (
                User $user,
                Department $department,
                Position $position,
                Team $team
            ) use (&$assignments, $branch) {

                $assignments[] = [
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'department_id' => $department->id,
                    'position_id' => $position->id,
                    'team_id' => $team->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            };

            // EXECUTIVE
            $pushAssignment($director, $executive, $positions['director'], $teams['executive']);
            $pushAssignment($generalManager, $executive, $positions['gm'], $teams['executive']);

            // EDITORIAL
            $pushAssignment($editorChief, $editorial, $positions['editor_chief'], $teams['editorial']);
            $pushAssignment($editor1, $editorial, $positions['editor'], $teams['editorial']);
            $pushAssignment($editor2, $editorial, $positions['editor'], $teams['editorial']);

            foreach ($reporters as $reporter) {
                $pushAssignment($reporter, $editorial, $positions['reporter'], $teams['editorial']);
            }

            // CREATIVE
            $pushAssignment($creativeHead, $creative, $positions['creative_head'], $teams['creative']);

            $pushAssignment($creativeUsers[0], $creative, $positions['video_staff'], $teams['creative']);
            $pushAssignment($creativeUsers[1], $creative, $positions['video_staff'], $teams['creative']);
            $pushAssignment($creativeUsers[2], $creative, $positions['designer'], $teams['creative']);
            $pushAssignment($creativeUsers[3], $creative, $positions['designer'], $teams['creative']);
            $pushAssignment($creativeUsers[4], $creative, $positions['motion'], $teams['creative']);

            // DIGITAL
            $pushAssignment($digitalHead, $digital, $positions['digital_head'], $teams['digital']);
            $pushAssignment($digitalStaff, $digital, $positions['social_media'], $teams['digital']);

            // MARKETING
            $pushAssignment($marketingHead, $marketing, $positions['marketing_head'], $teams['marketing']);
            $pushAssignment($marketingStaff, $marketing, $positions['marketing_staff'], $teams['marketing']);

            // HR
            $pushAssignment($operationalHead, $hr, $positions['operational_head'], $teams['hr']);
            $pushAssignment($hrHead, $hr, $positions['hr_head'], $teams['hr']);
            $pushAssignment($hrStaff1, $hr, $positions['hr_staff'], $teams['hr']);
            $pushAssignment($hrStaff2, $hr, $positions['hr_staff'], $teams['hr']);
            $pushAssignment($itSupport, $hr, $positions['it_support'], $teams['hr']);

            EmployeeAssignment::insert($assignments);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
