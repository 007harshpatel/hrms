<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $totalTeam = \App\Models\Employee::count();
    
    $user = auth()->user();
    $isManager = $user->hasAnyRole(['Admin', 'HR']);
    $employee = \App\Models\Employee::where('user_id', $user->id)->first();
    $employeeId = $employee ? $employee->id : null;

    if ($isManager) {
        $pendingLeaves = \App\Models\Leave::where('status', 'pending')->count();
        $unpaidPayslips = \App\Models\Payslip::where('status', 'generated')->count();
        $upcomingPayroll = $unpaidPayslips > 0 ? "{$unpaidPayslips} Pending" : 'Ready';
    } else {
        $pendingLeaves = $employeeId ? \App\Models\Leave::where('employee_id', $employeeId)->where('status', 'pending')->count() : 0;
        $upcomingPayroll = 'Ready';
    }

    $nextHolidayObj = \App\Models\Holiday::where('date', '>=', now())->orderBy('date')->first();
    $nextHoliday = $nextHolidayObj 
        ? \Carbon\Carbon::parse($nextHolidayObj->date)->diffForHumans(['parts' => 1, 'short' => false]) 
        : 'None scheduled';

    return Inertia::render('Dashboard', [
        'stats' => [
            'totalTeam' => $totalTeam,
            'pendingLeaves' => $pendingLeaves,
            'upcomingPayroll' => $upcomingPayroll,
            'nextHoliday' => $nextHoliday,
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\EmployeeController;

use App\Http\Controllers\LeaveController;

use App\Http\Controllers\PayrollController;

use App\Http\Controllers\HolidayController;

Route::middleware('auth')->group(function () {
    Route::middleware(['role:Admin|HR'])->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
    
    // Leave Management Routes
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::patch('/leaves/{leave}/status', [LeaveController::class, 'updateStatus'])->name('leaves.updateStatus');

    // Payroll Management Routes
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::middleware(['role:Admin|HR'])->group(function () {
        Route::patch('/payroll/structure/{employee}', [PayrollController::class, 'updateStructure'])->name('payroll.structure.update');
        Route::post('/payroll/generate/{employee}', [PayrollController::class, 'generatePayslip'])->name('payroll.generate');
        Route::patch('/payroll/payslips/{payslip}/pay', [PayrollController::class, 'markPaid'])->name('payroll.payslips.pay');
    });

    // Holiday Management Routes
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::middleware(['role:Admin|HR'])->group(function () {
        Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
        Route::put('/holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
        Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/employee', [ProfileController::class, 'updateEmployee'])->name('profile.employee.update');
});

require __DIR__.'/auth.php';
