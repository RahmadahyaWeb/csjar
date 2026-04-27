<?php

// app/Services/PayrollService.php

namespace App\Services;

use App\Models\Attendance;
use App\Models\EmployeeSalary;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function generatePeriod(int $userId, string $startDate, string $endDate)
    {
        return DB::transaction(function () use ($userId, $startDate, $endDate) {

            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // ======================
            // LOCK: ONLY DRAFT CAN BE REGENERATED
            // ======================
            $existing = Payroll::where([
                'user_id' => $userId,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ])->first();

            if ($existing && $existing->status !== 'draft') {
                throw new \Exception('Payroll already finalized');
            }

            // ======================
            // INIT OVERTIME → PENDING
            // ======================
            Attendance::where('user_id', $userId)
                ->whereBetween('date', [$start, $end])
                ->where('overtime_minutes', '>', 0)
                ->where('overtime_status', 'none')
                ->update([
                    'overtime_status' => 'pending',
                ]);

            // ======================
            // GET DATA
            // ======================
            $attendances = Attendance::where('user_id', $userId)
                ->whereBetween('date', [$start, $end])
                ->get();

            $salary = EmployeeSalary::where('user_id', $userId)
                ->latest('effective_date')
                ->firstOrFail();

            // ======================
            // WORKING DAYS
            // ======================
            $workingDays = $attendances
                ->whereNotIn('status', ['holiday'])
                ->count();

            $dailySalary = $salary->basic_salary / max(1, $workingDays);
            $ratePerMinute = $dailySalary / (8 * 60);

            // ======================
            // AGGREGATION
            // ======================
            $totalLate = $attendances->sum('late_minutes');
            $totalEarly = $attendances->sum('early_leave_minutes');
            $totalAbsent = $attendances->where('status', 'absent')->count();

            $totalOvertime = $attendances
                ->where('overtime_status', 'approved')
                ->sum('overtime_minutes');

            // ======================
            // CALCULATION
            // ======================
            $deductionLate = $totalLate * $ratePerMinute;
            $deductionEarly = $totalEarly * $ratePerMinute;
            $deductionAbsent = $totalAbsent * $dailySalary;

            $overtimePay = $totalOvertime * $ratePerMinute;

            $totalEarning = $salary->basic_salary + $overtimePay;
            $totalDeduction = $deductionLate + $deductionEarly + $deductionAbsent;

            $net = $totalEarning - $totalDeduction;

            // ======================
            // STORE PAYROLL
            // ======================
            $payroll = Payroll::updateOrCreate(
                [
                    'user_id' => $userId,
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                ],
                [
                    'total_earning' => $totalEarning,
                    'total_deduction' => $totalDeduction,
                    'net_salary' => $net,
                    'status' => 'draft',
                ]
            );

            $payroll->details()->delete();

            PayrollDetail::insert([
                [
                    'payroll_id' => $payroll->id,
                    'component_name' => 'Basic Salary',
                    'amount' => $salary->basic_salary,
                    'type' => 'earning',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'payroll_id' => $payroll->id,
                    'component_name' => 'Overtime (Approved)',
                    'amount' => $overtimePay,
                    'type' => 'earning',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'payroll_id' => $payroll->id,
                    'component_name' => 'Late Deduction',
                    'amount' => $deductionLate,
                    'type' => 'deduction',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'payroll_id' => $payroll->id,
                    'component_name' => 'Early Leave Deduction',
                    'amount' => $deductionEarly,
                    'type' => 'deduction',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'payroll_id' => $payroll->id,
                    'component_name' => 'Absent Deduction',
                    'amount' => $deductionAbsent,
                    'type' => 'deduction',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            Attendance::where('user_id', $userId)
                ->whereBetween('date', [$start, $end])
                ->update([
                    'is_locked' => true,
                ]);

            return $payroll;
        });
    }
}
