<?php

namespace App\Http\Controllers;

use App\Models\CouponGroup;
use Illuminate\Http\Request;

class CouponGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',CouponGroup::class);

        return Inertia('CouponGroup/Index',[
            'groups' => CouponGroup::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create',CouponGroup::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create',CouponGroup::class);

        $request->validate([
            'name' => 'required'
        ]);

        if($request->for_gifts) {
            $updateForGifts = CouponGroup::all()->each->update([
                'for_gifts' => false
            ]);
        }

        $group = CouponGroup::create($request->all());

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(CouponGroup $couponGroup)
    {
        $this->authorize('viewAny',CouponGroup::class);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CouponGroup $couponGroup)
    {
        $this->authorize('update',CouponGroup::class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CouponGroup $couponGroup)
    {
        $this->authorize('update',CouponGroup::class);

        $request->validate([
            'name' => 'required'
        ]);

        if($request->for_gifts) {
            $updateForGifts = CouponGroup::all()->each->update([
                'for_gifts' => false
            ]);
        }

        $couponGroup->update($request->all());

        return back();
    }

    public function delete(CouponGroup $couponGroup)
    {
        $this->authorize('delete',CouponGroup::class);

        $couponGroup->delete();
        return back();
    }

    public function restore(CouponGroup $couponGroup)
    {
        $this->authorize('restore',CouponGroup::class);

        $couponGroup->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CouponGroup $couponGroup)
    {
        $this->authorize('forceDelete',CouponGroup::class);
    }
}
