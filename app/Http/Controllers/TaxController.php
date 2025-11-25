<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->wantsJson() ){

            return Tax::all();

        }

        $this->authorize('viewAny',Tax::class);

        return Inertia('Tax/Index',[
            'taxes' => Tax::paginate(30),
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
        $this->authorize('create',Tax::class);

        $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $tax = Tax::create($request->all());

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Vergi oluşturuldu.',
            'data' => $tax
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $this->authorize('update',Tax::class);

        $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $tax->fill($request->all());
        $tax->save();

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Layout güncellendi.',
            'data' => $tax
        ]);
    }

    public function delete(Tax $tax)
    {
        $this->authorize('delete',Tax::class);

        $tax->delete();
        return back();
    }

    public function restore(Tax $tax)
    {
        $this->authorize('restore',Tax::class);

        $tax->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $this->authorize('forceDelete',Tax::class);

        $tax->forceDelete();
        return back();
    }
}
