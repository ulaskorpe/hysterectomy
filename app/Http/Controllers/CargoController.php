<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->wantsJson() ){

            return Cargo::all();

        }

        $this->authorize('viewAny', Cargo::class);

        return Inertia('Cargo/Index',[
            'cargos' => Cargo::paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Cargo::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Cargo::class);

        $request->validate([
            'name' => 'required',
            'fixed' => 'required',
        ]);

        $cargo = Cargo::create($request->all());

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Layout oluşturuldu.',
            'data' => $cargo
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cargo $cargo)
    {
        $this->authorize('view', Cargo::class);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cargo $cargo)
    {
        $this->authorize('update', Cargo::class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cargo $cargo)
    {
        $this->authorize('update', Cargo::class);

        $request->validate([
            'name' => 'required',
            'fixed' => 'required',
        ]);

        $cargo->fill($request->all());
        $cargo->save();

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Layout güncellendi.',
            'data' => $cargo
        ]);
    }

    public function delete(Cargo $cargo)
    {
        $this->authorize('delete', Cargo::class);

        $cargo->delete();
        return back();
    }

    public function restore(Cargo $cargo)
    {
        $this->authorize('restore', Cargo::class);

        $cargo->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cargo $cargo)
    {
        $this->authorize('forceDelete', Cargo::class);

        $cargo->forceDelete();
        return back();
    }
}
