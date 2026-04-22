<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    // Annual Leave limit
    private const ANNUAL_LEAVES = 12;

    public function index()
    {
        $user = Auth::user();
        $isManager = $user->hasRole(['Admin', 'HR']);

        if ($isManager) {
            $leaves = Leave::with(['employee.user'])
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($leave) {
                            return [
                                'id' => $leave->id,
                                'employee_name' => $leave->employee->first_name . ' ' . $leave->employee->last_name,
                                'start_date' => $leave->start_date,
                                'end_date' => $leave->end_date,
                                'reason' => $leave->reason,
                                'status' => $leave->status,
                                'created_at' => $leave->created_at->format('Y-m-d')
                            ];
                        });
            return Inertia::render('Leaves/IndexHR', [
                'leaves' => $leaves
            ]);
        } else {
            $employee_id = $user->employee->id;
            
            $history = Leave::where('employee_id', $employee_id)
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->map(function($leave) {
                                return [
                                    'id' => $leave->id,
                                    'start_date' => $leave->start_date,
                                    'end_date' => $leave->end_date,
                                    'reason' => $leave->reason,
                                    'status' => $leave->status,
                                    'created_at' => $leave->created_at->format('Y-m-d')
                                ];
                            });

            // Calculate total consumed days from pending and approved leaves
            $consumedDays = Leave::where('employee_id', $employee_id)
                                ->whereIn('status', ['pending', 'approved'])
                                ->get()
                                ->sum(function($leave) {
                                    $start = Carbon::parse($leave->start_date);
                                    $end = Carbon::parse($leave->end_date);
                                    return $start->diffInDays($end) + 1;
                                });

            $balance = [
                'allocated' => self::ANNUAL_LEAVES,
                'consumed' => $consumedDays,
                'balance' => max(0, self::ANNUAL_LEAVES - $consumedDays)
            ];

            return Inertia::render('Leaves/IndexEmployee', [
                'balance' => $balance,
                'history' => $history
            ]);
        }
    }

    public function create()
    {
        $user = Auth::user();
        $employee_id = $user->employee->id;
        
        $consumedDays = Leave::where('employee_id', $employee_id)
                                ->whereIn('status', ['pending', 'approved'])
                                ->get()
                                ->sum(function($leave) {
                                    $start = Carbon::parse($leave->start_date);
                                    $end = Carbon::parse($leave->end_date);
                                    return $start->diffInDays($end) + 1;
                                });

        $availableBalance = max(0, self::ANNUAL_LEAVES - $consumedDays);

        return Inertia::render('Leaves/Create', [
            'availableBalance' => $availableBalance
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $user = Auth::user();
        $employee_id = $user->employee->id;

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $daysRequested = $start->diffInDays($end) + 1; // Inclusive

        $consumedDays = Leave::where('employee_id', $employee_id)
                                ->whereIn('status', ['pending', 'approved'])
                                ->get()
                                ->sum(function($leave) {
                                    $lstart = Carbon::parse($leave->start_date);
                                    $lend = Carbon::parse($leave->end_date);
                                    return $lstart->diffInDays($lend) + 1;
                                });

        $availableBalance = max(0, self::ANNUAL_LEAVES - $consumedDays);

        if ($availableBalance < $daysRequested) {
            return back()->withErrors(['start_date' => "Insufficient leave balance. You requested $daysRequested days but only have $availableBalance days available."]);
        }

        Leave::create([
            'employee_id' => $employee_id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending'
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    public function updateStatus(Request $request, Leave $leave)
    {
        $user = Auth::user();
        if (!$user->hasRole(['Admin', 'HR'])) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        if ($leave->status !== 'pending') {
            return back()->withErrors(['status' => 'This leave request has already been processed.']);
        }

        $leave->status = $validated['status'];
        $leave->approved_by = $user->id;
        $leave->save();

        return back()->with('success', 'Leave status updated successfully.');
    }
}
