<?php

namespace App\Http\Controllers;

use App\Models\WorkingHour;
use Illuminate\Http\Request;
use App\Rules\CheckHourMinuteFormat;

class WorkingHourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->wantsJson() ){

            return WorkingHour::all();

        }

        return Inertia('WorkingHour/Index',[
            'working_hours' => WorkingHour::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkingHour $workingHour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkingHour $workingHour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkingHour $workingHour)
    {
        $request->validate([
            'start_time' => ['required',new CheckHourMinuteFormat],
            'end_time' => ['required',new CheckHourMinuteFormat],
        ]);

        $workingHour->update($request->all());

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Çalışma saati güncellendi.',
            'data' => $workingHour
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkingHour $workingHour)
    {
        //
    }
}
