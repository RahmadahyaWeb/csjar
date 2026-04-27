<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // ========================
            // PERMISSIONS
            // ========================
            $permissions = [
                'user.view', 'user.create', 'user.update', 'user.delete',
                'role.view', 'role.create', 'role.update', 'role.delete',

                'branch.view', 'branch.create', 'branch.update', 'branch.delete',
                'department.view', 'department.create', 'department.update', 'department.delete',
                'position.view', 'position.create', 'position.update', 'position.delete',
                'team.view', 'team.create', 'team.update', 'team.delete',
                'employee-assignment.view', 'employee-assignment.create', 'employee-assignment.update', 'employee-assignment.delete',

                'shift.view', 'shift.create', 'shift.update', 'shift.delete',
                'work-schedule.view', 'work-schedule.create', 'work-schedule.update', 'work-schedule.delete',
                'employee-schedule.view', 'employee-schedule.create', 'employee-schedule.update', 'employee-schedule.delete',

                'holiday.view', 'holiday.create', 'holiday.update', 'holiday.delete',

                'leave.view', 'leave.create', 'leave.update', 'leave.delete',
                'leave.approve', 'leave.conflict',

                'break-rule.view', 'break-rule.create', 'break-rule.update', 'break-rule.delete',

                'attendance-log.view',

                'my-attendance.view',

                'attendance-monitoring.view',
                'attendance-report.view',
            ];

            foreach ($permissions as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }

            // ========================
            // ROLES
            // ========================
            $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
            $head = Role::firstOrCreate(['name' => 'head']);
            $employee = Role::firstOrCreate(['name' => 'employee']);
            $hr = Role::firstOrCreate(['name' => 'hr']);

            // ========================
            // ASSIGN PERMISSIONS
            // ========================

            // SUPER ADMIN (full access)
            $superAdmin->syncPermissions($permissions);

            // HEAD (approval + visibility)
            $head->syncPermissions([
                'leave.view',
                'leave.approve',

                'attendance-log.view',

                'my-attendance.view',
            ]);

            // HR (full operational control)
            $hr->syncPermissions([
                // organization (read only)
                'branch.view',
                'department.view',
                'position.view',
                'team.view',
                'employee-assignment.view',

                // work config
                'shift.view', 'shift.create', 'shift.update',
                'work-schedule.view', 'work-schedule.create', 'work-schedule.update',
                'employee-schedule.view', 'employee-schedule.create', 'employee-schedule.update',
                'break-rule.view', 'break-rule.create', 'break-rule.update',

                // holiday
                'holiday.view', 'holiday.create', 'holiday.update',

                // leave
                'leave.view',
                'leave.approve',
                'leave.conflict',

                // attendance
                'attendance-log.view',
                'attendance-monitoring.view',
                'attendance-report.view',

                // personal
                'my-attendance.view',
            ]);

            // EMPLOYEE (self service only)
            $employee->syncPermissions([
                'leave.view',
                'leave.create',

                'my-attendance.view',
            ]);

            // ========================
            // USERS
            // ========================
            $superAdminUser = User::updateOrCreate(
                ['email' => 'superadmin@mail.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'),
                ]
            );

            // ========================
            // ASSIGN ROLE
            // ========================
            $superAdminUser->syncRoles([$superAdmin]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
