<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole(['Admin', 'HR'])) {
            $employees = Employee::with('salaryStructure')->get()->map(function($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'department' => $emp->department,
                    'salary_structure' => $emp->salaryStructure ? [
                        'base_salary' => $emp->salaryStructure->base_salary,
                        'hra_percentage' => $emp->salaryStructure->hra_percentage,
                        'allowances' => $emp->salaryStructure->allowances,
                        'deductions' => $emp->salaryStructure->deductions,
                    ] : null,
                ];
            });

            $payslips = Payslip::with('employee')->orderBy('year', 'desc')->orderBy('month', 'desc')->get()->map(function($p) {
                return [
                    'id' => $p->id,
                    'employee_name' => $p->employee->first_name . ' ' . $p->employee->last_name,
                    'period' => $p->month . '/' . $p->year,
                    'net_pay' => $p->net_pay,
                    'status' => $p->status,
                    'created_at' => $p->created_at->format('Y-m-d')
                ];
            });

            return Inertia::render('Payroll/IndexHR', [
                'employees' => $employees,
                'payslips' => $payslips,
            ]);
        } else {
            $employee = $user->employee;
            if (!$employee) {
                return Inertia::render('Payroll/IndexEmployee', [
                    'salary_structure' => null,
                    'payslips' => []
                ]);
            }

            $structure = $employee->salaryStructure;

            $payslips = $employee->payslips()->orderBy('year', 'desc')->orderBy('month', 'desc')->get()->map(function($p) {
                return [
                    'id' => $p->id,
                    'period' => sprintf("%02d/%d", $p->month, $p->year),
                    'basic_pay' => $p->basic_pay,
                    'hra' => $p->hra,
                    'allowances' => $p->allowances,
                    'deductions' => $p->deductions,
                    'net_pay' => $p->net_pay,
                    'status' => $p->status,
                    'created_at' => $p->created_at->format('Y-m-d')
                ];
            });

            return Inertia::render('Payroll/IndexEmployee', [
                'salary_structure' => $structure ? [
                    'base_salary' => $structure->base_salary,
                    'hra_percentage' => $structure->hra_percentage,
                    'allowances' => $structure->allowances,
                    'deductions' => $structure->deductions,
                ] : null,
                'payslips' => $payslips
            ]);
        }
    }

    public function updateStructure(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'hra_percentage' => 'required|numeric|min:0|max:100',
            'allowances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
        ]);

        SalaryStructure::updateOrCreate(
            ['employee_id' => $employee->id],
            $validated
        );

        return back()->with('success', 'Salary Structure updated successfully.');
    }

    public function generatePayslip(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $structure = $employee->salaryStructure;
        if (!$structure) {
            return back()->withErrors(['message' => 'Employee does not have a salary structure defined.']);
        }

        // Check if payslip already generated
        $existing = Payslip::where('employee_id', $employee->id)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($existing) {
            return back()->withErrors(['message' => 'Payslip already generated for this period.']);
        }

        $base = floatval($structure->base_salary);
        $hra_perc = floatval($structure->hra_percentage);
        $allowances = floatval($structure->allowances);
        $deductions = floatval($structure->deductions);

        $hra = $base * ($hra_perc / 100);
        $net_pay = $base + $hra + $allowances - $deductions;

        Payslip::create([
            'employee_id' => $employee->id,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'basic_pay' => $base,
            'hra' => $hra,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_pay' => $net_pay,
            'status' => 'generated'
        ]);

        return back()->with('success', 'Payslip generated successfully.');
    }

    public function markPaid(Request $request, Payslip $payslip)
    {
        if ($payslip->status === 'paid') {
            return back()->withErrors(['message' => 'Payslip is already paid.']);
        }

        $payslip->update(['status' => 'paid']);

        return back()->with('success', 'Payslip marked as paid.');
    }
}
