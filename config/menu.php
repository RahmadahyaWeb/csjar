<?php

return [

    'sidebar' => [

        // ======================
        // MAIN
        // ======================
        [
            'heading' => 'Main',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'home',
                    'route' => 'dashboard',
                    'permission' => null,
                ],
            ],
        ],

        // ======================
        // MY ACTIVITY (USER FIRST)
        // ======================
        [
            'heading' => 'My Activity',
            'items' => [
                [
                    'label' => 'Attendance',
                    'icon' => 'finger-print',
                    'route' => 'my-attendances.index',
                    'permission' => 'my-attendance.view',
                    'active' => ['my-attendances.index'],
                ],
                [
                    'label' => 'Attendance History',
                    'icon' => 'clock',
                    'route' => 'my-attendances.history',
                    'permission' => 'my-attendance.view',
                    'active' => ['my-attendances.history'],
                ],
                [
                    'label' => 'Face Setup',
                    'icon' => 'face-smile',
                    'route' => 'face.setup',
                    'permission' => 'face-setup.view',
                    'active' => ['face.setup*'],
                ],
            ],
        ],

        // ======================
        // LEAVE
        // ======================
        [
            'heading' => 'Leave',
            'items' => [
                [
                    'label' => 'Leave Requests',
                    'icon' => 'document-text',
                    'route' => 'leaves.index',
                    'permission' => 'leave.view',
                    'active' => ['leaves.index', 'leaves.create', 'leaves.edit'],
                ],
                [
                    'label' => 'Conflicts',
                    'icon' => 'exclamation-triangle',
                    'route' => 'leaves.conflicts',
                    'permission' => 'leave.conflict',
                    'active' => ['leaves.conflicts'],
                ],
            ],
        ],

        // ======================
        // PAYROLL
        // ======================
        [
            'heading' => 'Payroll',
            'items' => [
                [
                    'label' => 'Payroll',
                    'icon' => 'banknotes',
                    'route' => 'payrolls.index',
                    'permission' => 'payroll.view',
                    'active' => ['payrolls*'],
                ],
                [
                    'label' => 'Overtime Approval',
                    'icon' => 'check-badge',
                    'route' => 'overtime-approval.index',
                    'permission' => 'overtime-approval.view',
                    'active' => ['overtime-approval.index*'],
                ],
            ],
        ],

        // ======================
        // REPORT
        // ======================
        [
            'heading' => 'Reports',
            'items' => [
                [
                    'label' => 'Attendance Report',
                    'icon' => 'chart-bar',
                    'route' => 'report.attendance-report',
                    'permission' => 'attendance-report.view',
                    'active' => ['report.attendance-report'],
                ],
                [
                    'label' => 'Payroll Report',
                    'icon' => 'chart-pie',
                    'route' => 'report.payroll-report',
                    'permission' => 'payroll-report.view',
                    'active' => ['report.payroll-report'],
                ],
            ],
        ],

        // ======================
        // MONITORING
        // ======================
        [
            'heading' => 'Monitoring',
            'items' => [
                [
                    'label' => 'Attendance Monitoring',
                    'icon' => 'eye',
                    'route' => 'attendance-monitoring.index',
                    'permission' => 'attendance-monitoring.view',
                    'active' => ['attendance-monitoring.*'],
                ],
            ],
        ],

        // ======================
        // WORK CONFIG
        // ======================
        [
            'heading' => 'Work Setup',
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
                    'icon' => 'pause-circle',
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

        // ======================
        // ORGANIZATION
        // ======================
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
                    'icon' => 'user-group',
                    'route' => 'employee-assignments.index',
                    'permission' => 'employee-assignment.view',
                    'active' => ['employee-assignments.*'],
                ],
            ],
        ],

        // ======================
        // USERS & ACCESS
        // ======================
        [
            'heading' => 'Users & Access',
            'items' => [
                [
                    'label' => 'Users',
                    'icon' => 'users',
                    'route' => 'users.index',
                    'permission' => 'user.view',
                    'active' => ['users.*'],
                ],
                [
                    'label' => 'Roles & Permissions',
                    'icon' => 'shield-check',
                    'route' => 'roles.index',
                    'permission' => 'role.view',
                    'active' => ['roles.*'],
                ],
            ],
        ],

    ],

];
