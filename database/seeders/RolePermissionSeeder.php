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

                // user & role
                'user.view', 'user.create', 'user.update', 'user.delete',
                'role.view', 'role.create', 'role.update', 'role.delete',

                // organization
                'branch.view', 'branch.create', 'branch.update', 'branch.delete',
                'department.view', 'department.create', 'department.update', 'department.delete',
                'position.view', 'position.create', 'position.update', 'position.delete',
                'team.view', 'team.create', 'team.update', 'team.delete',
                'employee-assignment.view', 'employee-assignment.create', 'employee-assignment.update', 'employee-assignment.delete',

                // work setup
                'shift.view', 'shift.create', 'shift.update', 'shift.delete',
                'work-schedule.view', 'work-schedule.create', 'work-schedule.update', 'work-schedule.delete',
                'employee-schedule.view', 'employee-schedule.create', 'employee-schedule.update', 'employee-schedule.delete',
                'break-rule.view', 'break-rule.create', 'break-rule.update', 'break-rule.delete',
                'holiday.view', 'holiday.create', 'holiday.update', 'holiday.delete',

                // leave
                'leave.view', 'leave.create', 'leave.update', 'leave.delete',
                'leave.approve', 'leave.conflict',

                // attendance
                'attendance-log.view',
                'attendance-monitoring.view',
                'attendance-report.view',
                'my-attendance.view',

                // payroll
                'payroll.view',
                'payroll-report.view',
                'overtime-approval.view',

                // face
                'face-setup.view',

                'late-report.view',
                'overtime-report.view',
                'employee-report.view',
            ];

            foreach ($permissions as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }

            // ========================
            // ROLES
            // ========================
            $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
            $hr = Role::firstOrCreate(['name' => 'hr']);
            $hrStaff = Role::firstOrCreate(['name' => 'hr_staff']);
            $head = Role::firstOrCreate(['name' => 'head']);
            $employee = Role::firstOrCreate(['name' => 'employee']);

            // ========================
            // ASSIGN PERMISSIONS
            // ========================

            // SUPER ADMIN (FULL ACCESS)
            $superAdmin->syncPermissions($permissions);

            // HR (FULL HR CONTROL)
            $hr->syncPermissions([
                'branch.view', 'branch.create', 'branch.update',
                'department.view', 'department.create', 'department.update',
                'position.view', 'position.create', 'position.update',
                'team.view', 'team.create', 'team.update',
                'employee-assignment.view', 'employee-assignment.create', 'employee-assignment.update',

                'shift.view', 'shift.create', 'shift.update',
                'work-schedule.view', 'work-schedule.create', 'work-schedule.update',
                'employee-schedule.view', 'employee-schedule.create', 'employee-schedule.update',
                'break-rule.view', 'break-rule.create', 'break-rule.update',
                'holiday.view', 'holiday.create', 'holiday.update',

                'leave.view',
                'leave.approve',
                'leave.conflict',

                'attendance-log.view',
                'attendance-monitoring.view',
                'attendance-report.view',

                'payroll.view',
                'payroll-report.view',

                'overtime-approval.view',

                'my-attendance.view',

                'face-setup.view',

            ]);

            // HR STAFF (LIMITED OPERATIONAL)
            $hrStaff->syncPermissions([
                // organization (read only)
                'branch.view',
                'department.view',
                'position.view',
                'team.view',
                'employee-assignment.view',

                // work setup (partial)
                'shift.view',
                'work-schedule.view',
                'employee-schedule.view',
                'break-rule.view',
                'holiday.view',

                // leave
                'leave.view',

                // attendance
                'attendance-log.view',
                'attendance-monitoring.view',

                // payroll (view only)
                'payroll.view',

                // overtime
                'overtime-approval.view',

                // self
                'my-attendance.view',

                // face
                'face-setup.view',
            ]);

            // HEAD (APPROVAL)
            $head->syncPermissions([
                'leave.view',
                'leave.approve',

                'attendance-log.view',

                'overtime-approval.view',

                'my-attendance.view',

                'face-setup.view',
            ]);

            // EMPLOYEE (SELF SERVICE)
            $employee->syncPermissions([
                'leave.view',
                'leave.create',

                'my-attendance.view',

                'face-setup.view',
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
