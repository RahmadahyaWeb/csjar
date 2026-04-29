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
            // PERMISSIONS (GROUPED)
            // ========================
            $permissions = [

                // USER & ROLE
                'user.view', 'user.create', 'user.update', 'user.delete',
                'role.view', 'role.create', 'role.update', 'role.delete',

                // ORGANIZATION
                'branch.view', 'branch.create', 'branch.update', 'branch.delete',
                'department.view', 'department.create', 'department.update', 'department.delete',
                'position.view', 'position.create', 'position.update', 'position.delete',
                'team.view', 'team.create', 'team.update', 'team.delete',
                'employee-assignment.view', 'employee-assignment.create', 'employee-assignment.update', 'employee-assignment.delete',

                // WORK SETUP
                'shift.view', 'shift.create', 'shift.update', 'shift.delete',
                'work-schedule.view', 'work-schedule.create', 'work-schedule.update', 'work-schedule.delete',
                'employee-schedule.view', 'employee-schedule.create', 'employee-schedule.update', 'employee-schedule.delete',
                'break-rule.view', 'break-rule.create', 'break-rule.update', 'break-rule.delete',
                'holiday.view', 'holiday.create', 'holiday.update', 'holiday.delete',

                // LEAVE
                'leave.view', 'leave.create', 'leave.update', 'leave.delete',
                'leave.approve', 'leave.conflict',

                // ATTENDANCE
                'attendance-log.view',
                'attendance-monitoring.view',
                'attendance-report.view',
                'my-attendance.view',

                // PAYROLL
                'payroll.view',
                'payroll-report.view',
                'overtime-approval.view',

                // FACE
                'face-setup.view',

                // REPORTS
                'late-report.view',
                'overtime-report.view',
                'employee-report.view',
                'employee-schedule-report.view',
                'leave-report.view',
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
            // SUPER ADMIN (FULL ACCESS)
            // ========================
            $superAdmin->syncPermissions($permissions);

            // ========================
            // HR (FULL HR MANAGEMENT)
            // ========================
            $hr->syncPermissions([

                // organization (full except delete optional)
                'branch.view', 'branch.create', 'branch.update',
                'department.view', 'department.create', 'department.update',
                'position.view', 'position.create', 'position.update',
                'team.view', 'team.create', 'team.update',
                'employee-assignment.view', 'employee-assignment.create', 'employee-assignment.update',

                // work setup
                'shift.view', 'shift.create', 'shift.update',
                'work-schedule.view', 'work-schedule.create', 'work-schedule.update',
                'employee-schedule.view', 'employee-schedule.create', 'employee-schedule.update',
                'break-rule.view', 'break-rule.create', 'break-rule.update',
                'holiday.view', 'holiday.create', 'holiday.update',

                // leave
                'leave.view', 'leave.approve', 'leave.conflict',

                // attendance
                'attendance-log.view',
                'attendance-monitoring.view',
                'attendance-report.view',

                // payroll
                'payroll.view',
                'payroll-report.view',
                'overtime-approval.view',

                // reports
                'late-report.view',
                'overtime-report.view',
                'employee-report.view',
                'employee-schedule-report.view',
                'leave-report.view',

                // self
                'my-attendance.view',
                'face-setup.view',
            ]);

            // ========================
            // HR STAFF (OPERATIONAL - LIMITED)
            // ========================
            $hrStaff->syncPermissions([

                // organization (read only)
                'branch.view',
                'department.view',
                'position.view',
                'team.view',
                'employee-assignment.view',

                // work setup (read only)
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

                // payroll (view)
                'payroll.view',

                // reports (view)
                'late-report.view',
                'overtime-report.view',
                'employee-report.view',
                'employee-schedule-report.view',
                'leave-report.view',

                // overtime approval (view only context)
                'overtime-approval.view',

                // self
                'my-attendance.view',
                'face-setup.view',
            ]);

            // ========================
            // HEAD (APPROVAL + MONITORING)
            // ========================
            $head->syncPermissions([

                // leave
                'leave.view',
                'leave.approve',

                // attendance
                'attendance-log.view',
                'attendance-monitoring.view',

                // overtime
                'overtime-approval.view',

                // reports (limited visibility)
                'late-report.view',
                'overtime-report.view',
                'leave-report.view',

                // self
                'my-attendance.view',
                'face-setup.view',
            ]);

            // ========================
            // EMPLOYEE (SELF SERVICE)
            // ========================
            $employee->syncPermissions([

                // leave
                'leave.view',
                'leave.create',

                // self attendance
                'my-attendance.view',

                // face
                'face-setup.view',
            ]);

            // ========================
            // DEFAULT USER
            // ========================
            $superAdminUser = User::updateOrCreate(
                ['email' => 'superadmin@mail.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'),
                ]
            );

            $superAdminUser->syncRoles([$superAdmin]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
