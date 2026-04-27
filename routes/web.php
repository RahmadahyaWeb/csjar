<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // ROLES & PERMISSIONS
    Route::prefix('roles')
        ->name('roles.')
        ->middleware(['permission:role.view'])
        ->group(function () {

            Route::livewire('/', 'pages::role.index')
                ->name('index');

            Route::livewire('/create', 'pages::role.form')
                ->middleware('permission:role.create')
                ->name('create');

            Route::livewire('/{role}/edit', 'pages::role.form')
                ->middleware('permission:role.update')
                ->name('edit');

        });

    // USER MANAGEMENT
    Route::prefix('users')
        ->name('users.')
        ->middleware(['permission:user.view'])
        ->group(function () {

            Route::livewire('/', 'pages::user.index')
                ->name('index');

            Route::livewire('/create', 'pages::user.form')
                ->middleware('permission:user.create')
                ->name('create');

            Route::livewire('/{user}/edit', 'pages::user.form')
                ->middleware('permission:user.update')
                ->name('edit');
        });

    // BRANCH MANAGEMENT
    Route::prefix('branches')
        ->name('branches.')
        ->middleware(['permission:branch.view'])
        ->group(function () {

            Route::livewire('/', 'pages::branch.index')
                ->name('index');

            Route::livewire('/create', 'pages::branch.form')
                ->middleware('permission:branch.create')
                ->name('create');

            Route::livewire('/{branch}/edit', 'pages::branch.form')
                ->middleware('permission:branch.update')
                ->name('edit');
        });

    // DEPARTMENT MANAGEMENT
    Route::prefix('departments')
        ->name('departments.')
        ->middleware(['permission:department.view'])
        ->group(function () {

            Route::livewire('/', 'pages::department.index')
                ->name('index');

            Route::livewire('/create', 'pages::department.form')
                ->middleware('permission:department.create')
                ->name('create');

            Route::livewire('/{department}/edit', 'pages::department.form')
                ->middleware('permission:department.update')
                ->name('edit');
        });

    // POSITION MANAGEMENT
    Route::prefix('positions')
        ->name('positions.')
        ->middleware(['permission:position.view'])
        ->group(function () {

            Route::livewire('/', 'pages::position.index')
                ->name('index');

            Route::livewire('/create', 'pages::position.form')
                ->middleware('permission:position.create')
                ->name('create');

            Route::livewire('/{position}/edit', 'pages::position.form')
                ->middleware('permission:position.update')
                ->name('edit');
        });

    // TEAM MANAGEMENT
    Route::prefix('teams')
        ->name('teams.')
        ->middleware(['permission:team.view'])
        ->group(function () {

            Route::livewire('/', 'pages::team.index')
                ->name('index');

            Route::livewire('/create', 'pages::team.form')
                ->middleware('permission:team.create')
                ->name('create');

            Route::livewire('/{team}/edit', 'pages::team.form')
                ->middleware('permission:team.update')
                ->name('edit');
        });

    // EMPLOYEE ASSIGNMENT MANAGEMENT
    Route::prefix('employee-assignments')
        ->name('employee-assignments.')
        ->middleware(['permission:employee-assignment.view'])
        ->group(function () {

            Route::livewire('/', 'pages::employee-assignment.index')
                ->name('index');

            Route::livewire('/create', 'pages::employee-assignment.form')
                ->middleware('permission:employee-assignment.create')
                ->name('create');

            Route::livewire('/{assignment}/edit', 'pages::employee-assignment.form')
                ->middleware('permission:employee-assignment.update')
                ->name('edit');
        });

    // SHIFT MANAGEMENT
    Route::prefix('shifts')
        ->name('shifts.')
        ->middleware(['permission:shift.view'])
        ->group(function () {

            Route::livewire('/', 'pages::shift.index')
                ->name('index');

            Route::livewire('/create', 'pages::shift.form')
                ->middleware('permission:shift.create')
                ->name('create');

            Route::livewire('/{shift}/edit', 'pages::shift.form')
                ->middleware('permission:shift.update')
                ->name('edit');
        });

    // WORK SCHEDULE MANAGEMENT
    Route::prefix('work-schedules')
        ->name('work-schedules.')
        ->middleware(['permission:work-schedule.view'])
        ->group(function () {

            Route::livewire('/', 'pages::work-schedule.index')
                ->name('index');

            Route::livewire('/create', 'pages::work-schedule.form')
                ->middleware('permission:work-schedule.create')
                ->name('create');

            Route::livewire('/{schedule}/edit', 'pages::work-schedule.form')
                ->middleware('permission:work-schedule.update')
                ->name('edit');
        });

    // EMPLOYEE SCHEDULE MANAGEMENT
    Route::prefix('employee-schedules')
        ->name('employee-schedules.')
        ->middleware(['permission:employee-schedule.view'])
        ->group(function () {

            Route::livewire('/', 'pages::employee-schedule.index')
                ->name('index');

            Route::livewire('/create', 'pages::employee-schedule.form')
                ->middleware('permission:employee-schedule.create')
                ->name('create');

            Route::livewire('/{schedule}/edit', 'pages::employee-schedule.form')
                ->middleware('permission:employee-schedule.update')
                ->name('edit');
        });

    // HOLIDAY MANAGEMENT
    Route::prefix('holidays')
        ->name('holidays.')
        ->middleware(['permission:holiday.view'])
        ->group(function () {

            Route::livewire('/', 'pages::holiday.index')
                ->name('index');

            Route::livewire('/create', 'pages::holiday.form')
                ->middleware('permission:holiday.create')
                ->name('create');

            Route::livewire('/{holiday}/edit', 'pages::holiday.form')
                ->middleware('permission:holiday.update')
                ->name('edit');
        });

    // LEAVE MANAGEMENT
    Route::prefix('leaves')
        ->name('leaves.')
        ->middleware(['permission:leave.view'])
        ->group(function () {

            Route::livewire('/', 'pages::leave.index')
                ->name('index');

            Route::livewire('/create', 'pages::leave.form')
                ->middleware('permission:leave.create')
                ->name('create');

            Route::livewire('/conflicts', 'pages::leave.conflict')
                ->middleware('permission:leave.conflict')
                ->name('conflicts');

            Route::livewire('/{leave}/edit', 'pages::leave.form')
                ->middleware('permission:leave.update')
                ->name('edit');
        });

    // BREAK RULE MANAGEMENT
    Route::prefix('break-rules')
        ->name('break-rules.')
        ->middleware(['permission:break-rule.view'])
        ->group(function () {

            Route::livewire('/', 'pages::break-rule.index')
                ->name('index');

            Route::livewire('/create', 'pages::break-rule.form')
                ->middleware('permission:break-rule.create')
                ->name('create');

            Route::livewire('/{break-rule}/edit', 'pages::break-rule.form')
                ->middleware('permission:break-rule.update')
                ->name('edit');
        });

    // ATTENDANCE LOGS
    Route::prefix('attendance-logs')
        ->name('attendance-logs.')
        ->middleware(['permission:attendance-log.view'])
        ->group(function () {

            Route::livewire('/', 'pages::attendance-log.index')
                ->name('index');
        });

    // MY ATTENDANCE
    Route::prefix('my-attendances')
        ->name('my-attendances.')
        ->middleware(['permission:my-attendance.view'])
        ->group(function () {

            Route::livewire('/', 'pages::my-attendance.index')
                ->name('index');

            Route::livewire('/history', 'pages::my-attendance-history.index')
                ->name('history');
        });

    // ATTENDANCE MONITORING
    Route::middleware(['auth'])
        ->name('attendance-monitoring.')
        ->middleware(['permission:attendance-monitoring.view'])
        ->prefix('attendance-monitoring')
        ->group(function () {
            Route::livewire('/', 'pages::attendance-monitoring.index')
                ->name('index');
        });

    // PAYROLL
    Route::middleware(['auth'])
        ->name('payrolls.')
        ->prefix('payrolls')
        ->group(function () {
            Route::livewire('/', 'pages::payroll.index')
                ->middleware('permission:payroll.view')
                ->name('index');

            Route::livewire('/{payroll}', 'pages::payroll.detail')
                ->middleware('permission:payroll.view')
                ->name('detail');
        });

    // REPORT
    Route::middleware(['auth'])
        ->name('overtime-approval.')
        ->prefix('overtime-approval')
        ->group(function () {
            Route::livewire('/', 'pages::overtime-approval.index')
                ->middleware('permission:overtime-approval.view')
                ->name('index');
        });

    // REPORT
    Route::middleware(['auth'])
        ->name('report.')
        ->prefix('report')
        ->group(function () {
            Route::livewire('/attendance-report', 'pages::report.attendance-report')
                ->middleware('permission:attendance-report.view')
                ->name('attendance-report');
        });
});

require __DIR__.'/settings.php';
