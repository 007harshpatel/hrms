<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\JobApplicationController;

Route::post('/contact-us', [ContactUsController::class, 'store']);
Route::post('/job-applications', [JobApplicationController::class, 'store']);
