<?php

return [

    'sidebar' => [

        [
            'heading' => 'Platform',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'home',
                    'route' => 'dashboard',
                    'permission' => null,
                ],
            ],
        ],

        [
            'heading' => 'Access Control',
            'items' => [
                [
                    'label' => 'Roles & Permissions',
                    'icon' => 'shield-check',
                    'route' => 'roles.index',
                    'permission' => 'role.view',
                    'active' => [
                        'roles.*',
                    ],
                ],
            ],
        ],

        [
            'heading' => 'User Management',
            'items' => [
                [
                    'label' => 'Users',
                    'icon' => 'users',
                    'route' => 'users.index',
                    'permission' => 'user.view',
                    'active' => [
                        'users.*',
                    ],
                ],
            ],
        ],

        [
            'heading' => 'Organization',
            'items' => [
                [
                    'label' => 'Branches',
                    'icon' => 'building-office-2',
                    'route' => 'branches.index',
                    'permission' => 'branch.view',
                    'active' => ['branches.*'],
                ],
                [
                    'label' => 'Departments',
                    'icon' => 'building-office',
                    'route' => 'departments.index',
                    'permission' => 'department.view',
                    'active' => ['departments.*'],
                ],
                [
                    'label' => 'Positions',
                    'icon' => 'briefcase',
                    'route' => 'positions.index',
                    'permission' => 'position.view',
                    'active' => ['positions.*'],
                ],
                [
                    'label' => 'Teams',
                    'icon' => 'rectangle-group',
                    'route' => 'teams.index',
                    'permission' => 'team.view',
                    'active' => ['teams.*'],
                ],
                [
                    'label' => 'Employee',
                    'icon' => 'user-plus',
                    'route' => 'employee-assignments.index',
                    'permission' => 'employee-assignment.view',
                    'active' => ['employee-assignments.*'],
                ],
            ],
        ],

        // DIPINDAHKAN KE ATAS (OPERASIONAL USER)
        [
            'heading' => 'My Activities',
            'items' => [
                [
                    'label' => 'My Attendance',
                    'icon' => 'document-text',
                    'route' => 'my-attendances.index',
                    'permission' => 'my-attendance.view',
                    'active' => ['my-attendances.index'],
                ],
                [
                    'label' => 'Attendance History',
                    'icon' => 'document-text',
                    'route' => 'my-attendances.history',
                    'permission' => 'my-attendance.view',
                    'active' => ['my-attendances.history'],
                ],
            ],
        ],

        [
            'heading' => 'Work Configuration',
            'items' => [
                [
                    'label' => 'Shifts',
                    'icon' => 'clock',
                    'route' => 'shifts.index',
                    'permission' => 'shift.view',
                    'active' => ['shifts.*'],
                ],
                [
                    'label' => 'Break Rules',
                    'icon' => 'pause',
                    'route' => 'break-rules.index',
                    'permission' => 'break-rule.view',
                    'active' => ['break-rules.*'],
                ],
                [
                    'label' => 'Work Schedules',
                    'icon' => 'calendar-days',
                    'route' => 'work-schedules.index',
                    'permission' => 'work-schedule.view',
                    'active' => ['work-schedules.*'],
                ],
                [
                    'label' => 'Employee Schedules',
                    'icon' => 'calendar',
                    'route' => 'employee-schedules.index',
                    'permission' => 'employee-schedule.view',
                    'active' => ['employee-schedules.*'],
                ],
                [
                    'label' => 'Holidays',
                    'icon' => 'calendar',
                    'route' => 'holidays.index',
                    'permission' => 'holiday.view',
                    'active' => ['holidays.*'],
                ],
            ],
        ],

        [
            'heading' => 'Leave Management',
            'items' => [
                [
                    'label' => 'Leave Requests',
                    'icon' => 'document-text',
                    'route' => 'leaves.index',
                    'permission' => 'leave.view',
                    'active' => ['leaves.index', 'leaves.create', 'leaves.edit'],
                ],
                [
                    'label' => 'Leave Conflicts',
                    'icon' => 'exclamation-triangle',
                    'route' => 'leaves.conflicts',
                    'permission' => 'leave.conflict',
                    'active' => ['leaves.conflicts'],
                ],
            ],
        ],

        [
            'heading' => 'Monitoring',
            'items' => [
                [
                    'label' => 'Attendance Monitoring',
                    'icon' => 'computer-desktop',
                    'route' => 'attendance-monitoring.index',
                    'permission' => 'attendance-monitoring.view',
                    'active' => ['attendance-monitoring.*'],
                ],
            ],
        ],

        [
            'heading' => 'Report',
            'items' => [
                [
                    'label' => 'Attendances Report',
                    'icon' => 'computer-desktop',
                    'route' => 'report.attendance-report',
                    'permission' => 'attendance-report.view',
                    'active' => ['report.attendance-report'],
                ],

                [
                    'label' => 'Payrolls Report',
                    'icon' => 'computer-desktop',
                    'route' => 'report.payroll-report',
                    'permission' => 'payroll-report.view',
                    'active' => ['report.payroll-report'],
                ],
            ],
        ],

        [
            'heading' => 'Payroll',
            'items' => [
                [
                    'label' => 'Payroll',
                    'icon' => 'document',
                    'route' => 'payrolls.index',
                    'permission' => 'payroll.view',
                    'active' => ['payrolls*'],
                ],

                [
                    'label' => 'Overtime Approval',
                    'icon' => 'document',
                    'route' => 'overtime-approval.index',
                    'permission' => 'overtime-approval.view',
                    'active' => ['overtime-approval.index*'],
                ],
            ],
        ],

    ],

];
