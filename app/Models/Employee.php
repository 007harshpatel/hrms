<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    public function user() { return $this->belongsTo(User::class); }
    public function manager() { return $this->belongsTo(Employee::class, 'manager_id'); }
    public function subordinates() { return $this->hasMany(Employee::class, 'manager_id'); }
    public function qualifications() { return $this->hasMany(EmployeeQualification::class); }
    public function previousOrganizations() { return $this->hasMany(EmployeePreviousOrganization::class); }
    public function salaryStructure() { return $this->hasOne(SalaryStructure::class); }
    public function payslips() { return $this->hasMany(Payslip::class); }
}
