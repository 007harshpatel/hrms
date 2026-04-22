<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user.roles', 'manager.user'])->latest()->get()->map(function ($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->first_name . ' ' . $emp->last_name,
                'email' => $emp->user->email ?? 'N/A',
                'role' => $emp->user && $emp->user->roles->count() > 0 ? $emp->user->roles->first()->name : 'User',
                'department' => $emp->department,
                'manager' => $emp->manager && $emp->manager->user ? $emp->manager->user->name : 'None',
                'joining_date' => $emp->joining_date,
            ];
        });

        $user = Auth::user();
        $canManage = $user ? $user->hasRole(['Admin', 'HR']) : false;

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
            'canManage' => $canManage,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        $managers = Employee::with('user')->get()->map(function($emp) {
            return ['id' => $emp->id, 'name' => $emp->first_name . ' ' . $emp->last_name];
        });

        return Inertia::render('Employees/Create', [
            'managers' => $managers
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:Admin,HR,Employee',
            'department' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'manager_id' => 'nullable|exists:employees,id',
            'contact_no' => 'required|string|max:20',
            'alternate_no' => 'nullable|string|max:20',
            'current_address' => 'required|string',
            'permanent_address' => 'required|string',
            'skills' => 'nullable|string',
            'experience' => 'nullable|string',
            
            // Dynamic Arrays validation
            'qualifications' => 'nullable|array',
            'qualifications.*.degree' => 'required_with:qualifications|string',
            'qualifications.*.institution' => 'required_with:qualifications|string',
            'qualifications.*.year' => 'required_with:qualifications|string',
            
            'previous_organizations' => 'nullable|array',
            'previous_organizations.*.company_name' => 'required_with:previous_organizations|string',
            'previous_organizations.*.designation' => 'required_with:previous_organizations|string',
            'previous_organizations.*.duration' => 'required_with:previous_organizations|string',
            
            // Files
            'professional_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
            'casual_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
            'adhar_upload' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5000',
            
            // Salary slips arrays (nested files in FormData need special handling if they arrive as top-level array indexing)
            'salary_slips.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5000',
        ]);

        $newUser = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $newUser->assignRole($validated['role']);

        $employee = Employee::create([
            'user_id' => $newUser->id,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'contact_no' => $validated['contact_no'],
            'alternate_no' => $validated['alternate_no'],
            'current_address' => $validated['current_address'],
            'permanent_address' => $validated['permanent_address'],
            'skills' => $validated['skills'],
            'experience' => $validated['experience'],
            'department' => $validated['department'],
            'joining_date' => $validated['joining_date'],
            'manager_id' => $validated['manager_id'],
        ]);

        // Save Qualifications
        if (!empty($validated['qualifications'])) {
            foreach ($validated['qualifications'] as $q) {
                $employee->qualifications()->create([
                    'qualification' => $q['degree'],
                    'institution' => $q['institution'],
                    'year' => $q['year'],
                ]);
            }
        }

        // Save Previous Organizations
        if (!empty($validated['previous_organizations'])) {
            foreach ($validated['previous_organizations'] as $index => $po) {
                $org = $employee->previousOrganizations()->create([
                    'company_name' => $po['company_name'],
                    'designation' => $po['designation'],
                    'duration' => $po['duration'],
                ]);

                // Store related salary slip if uploaded
                if ($request->hasFile("salary_slips.{$index}")) {
                    $path = $request->file("salary_slips.{$index}")->store('documents', 'public');
                    Document::create([
                        'employee_id' => $employee->id,
                        'type' => 'salary_slip_' . $org->id,
                        'path' => $path
                    ]);
                }
            }
        }

        // Handle Standalone Files
        $fileTypes = ['professional_photo', 'casual_photo', 'adhar_upload'];
        foreach ($fileTypes as $type) {
            if ($request->hasFile($type)) {
                $path = $request->file($type)->store('documents', 'public');
                Document::create([
                    'employee_id' => $employee->id,
                    'type' => $type,
                    'path' => $path
                ]);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        $employee->load(['user.roles', 'qualifications', 'previousOrganizations']);
        
        $managers = Employee::with('user')->where('id', '!=', $employee->id)->get()->map(function($emp) {
            return ['id' => $emp->id, 'name' => $emp->first_name . ' ' . $emp->last_name];
        });

        return Inertia::render('Employees/Edit', [
            'employee' => $employee,
            'managers' => $managers
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$employee->user_id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:Admin,HR,Employee',
            'department' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'manager_id' => 'nullable|exists:employees,id',
            'contact_no' => 'required|string|max:20',
            'alternate_no' => 'nullable|string|max:20',
            'current_address' => 'required|string',
            'permanent_address' => 'required|string',
            'skills' => 'nullable|string',
            'experience' => 'nullable|string',
            
            'qualifications' => 'nullable|array',
            'qualifications.*.degree' => 'required_with:qualifications|string',
            'qualifications.*.institution' => 'required_with:qualifications|string',
            'qualifications.*.year' => 'required_with:qualifications|string',
            
            'previous_organizations' => 'nullable|array',
            'previous_organizations.*.company_name' => 'required_with:previous_organizations|string',
            'previous_organizations.*.designation' => 'required_with:previous_organizations|string',
            'previous_organizations.*.duration' => 'required_with:previous_organizations|string',
            
            'professional_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
            'casual_photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
            'adhar_upload' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5000',
            'salary_slips.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5000',
        ]);

        $employeeUser = $employee->user;
        $employeeUser->name = $validated['first_name'] . ' ' . $validated['last_name'];
        $employeeUser->email = $validated['email'];
        if (!empty($validated['password'])) {
            $employeeUser->password = bcrypt($validated['password']);
        }
        $employeeUser->save();

        $employeeUser->syncRoles([$validated['role']]);

        $employee->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'contact_no' => $validated['contact_no'],
            'alternate_no' => $validated['alternate_no'],
            'current_address' => $validated['current_address'],
            'permanent_address' => $validated['permanent_address'],
            'skills' => $validated['skills'],
            'experience' => $validated['experience'],
            'department' => $validated['department'],
            'joining_date' => $validated['joining_date'],
            'manager_id' => $validated['manager_id'],
        ]);

        $employee->qualifications()->delete();
        if (!empty($validated['qualifications'])) {
            foreach ($validated['qualifications'] as $q) {
                $employee->qualifications()->create([
                    'qualification' => $q['degree'],
                    'institution' => $q['institution'],
                    'year' => $q['year'],
                ]);
            }
        }

        $employee->previousOrganizations()->delete();
        if (!empty($validated['previous_organizations'])) {
            foreach ($validated['previous_organizations'] as $index => $po) {
                $org = $employee->previousOrganizations()->create([
                    'company_name' => $po['company_name'],
                    'designation' => $po['designation'],
                    'duration' => $po['duration'],
                ]);

                if ($request->hasFile("salary_slips.{$index}")) {
                    $path = $request->file("salary_slips.{$index}")->store('documents', 'public');
                    Document::create([
                        'employee_id' => $employee->id,
                        'type' => 'salary_slip_' . $org->id,
                        'path' => $path
                    ]);
                }
            }
        }

        $fileTypes = ['professional_photo', 'casual_photo', 'adhar_upload'];
        foreach ($fileTypes as $type) {
            if ($request->hasFile($type)) {
                $path = $request->file($type)->store('documents', 'public');
                Document::create([
                    'employee_id' => $employee->id,
                    'type' => $type,
                    'path' => $path
                ]);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($employee->user_id === $user->id) {
            return redirect()->route('employees.index')->with('error', 'You cannot delete yourself.');
        }

        $employeeUser = $employee->user;
        $employee->delete();
        if ($employeeUser) {
            $employeeUser->delete();
        }

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
