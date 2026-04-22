<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'asc')->get();
        $user = Auth::user();
        $canManage = $user ? $user->hasRole(['Admin', 'HR']) : false;

        return Inertia::render('Holidays/Index', [
            'holidays' => $holidays,
            'canManage' => $canManage,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|string|in:National,Optional,Regional',
            'description' => 'nullable|string',
        ]);

        Holiday::create($validated);

        return redirect()->route('holidays.index')->with('success', 'Holiday created successfully.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|string|in:National,Optional,Regional',
            'description' => 'nullable|string',
        ]);

        $holiday->update($validated);

        return redirect()->route('holidays.index')->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR'])) {
            abort(403, 'Unauthorized action.');
        }

        $holiday->delete();

        return redirect()->route('holidays.index')->with('success', 'Holiday deleted successfully.');
    }
}
