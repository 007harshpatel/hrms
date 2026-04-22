<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\SalaryStructure;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $hrRole = Role::create(['name' => 'HR']);
        $employeeRole = Role::create(['name' => 'Employee']);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole($adminRole);
        $adminEmployee = Employee::create([
            'user_id' => $adminUser->id, 
            'department' => 'Management', 
            'joining_date' => now(),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'contact_no' => '9999999999',
            'current_address' => 'Mumbai, MH',
            'permanent_address' => 'Mumbai, MH'
        ]);

        $hrUser = User::factory()->create([
            'name' => 'HR User',
            'email' => 'hr@example.com',
            'password' => bcrypt('password'),
        ]);
        $hrUser->assignRole($hrRole);
        Employee::create([
            'user_id' => $hrUser->id, 
            'department' => 'HR', 
            'manager_id' => $adminEmployee->id, 
            'joining_date' => now(),
            'first_name' => 'HR',
            'last_name' => 'Manager',
            'contact_no' => '8888888888',
            'current_address' => 'Delhi, DL',
            'permanent_address' => 'Delhi, DL'
        ]);

        $empUser = User::factory()->create([
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ]);
        $empUser->assignRole($employeeRole);
        $testEmployee = Employee::create([
            'user_id' => $empUser->id, 
            'department' => 'Engineering', 
            'manager_id' => $adminEmployee->id, 
            'joining_date' => now(),
            'first_name' => 'Test',
            'last_name' => 'Employee',
            'contact_no' => '7777777777',
            'current_address' => 'Pune, MH',
            'permanent_address' => 'Pune, MH'
        ]);

        $annualLeave = LeaveType::create(['name' => 'Annual Leave', 'days_allocated' => 12]);

        $employees = Employee::all();
        foreach ($employees as $emp) {
            \App\Models\LeaveBalance::create(['employee_id' => $emp->id, 'leave_type_id' => $annualLeave->id, 'balance' => $annualLeave->days_allocated]);
        }

        SalaryStructure::create(['employee_id' => $testEmployee->id, 'base_salary' => 50000, 'hra_percentage' => 40, 'allowances' => 10000, 'deductions' => 2000]);
    }
}
