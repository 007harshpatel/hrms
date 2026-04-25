<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'job_applications';
    
    protected $fillable = [
        'role',
        'name',
        'email',
        'phone',
        'experience',
        'portfolio',
        'coverLetter',
        'notes',
        'status',
    ];
}
