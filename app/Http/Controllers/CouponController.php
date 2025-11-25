<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponGroup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ExportCoupons;
use App\Models\ProductType;
use App\Rules\CheckDateFormat;
use Illuminate\Validation\Rule;
use App\Models\Scopes\LanguageScope;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $this->authorize('viewAny',Coupon::class);

        $coupons_query = QueryBuilder::for(Coupon::class)
        ->with(['group','product_types:id,name,language,uuid'])
        ->when($request->input('search'), function ($query, $search) {
            $query->where('code','like','%'.$search.'%');
        })
        ->defaultSort('-created_at')
        ->allowedSorts('created_at')
        ->allowedFilters([
            AllowedFilter::callback('coupon_group_id', function (Builder $query, $value) {
                $query->whereIn('coupon_group_id', $value);
            }),
        ]);

        if ($request->has('export') && $request->export === "true") {

            $coupons = $coupons_query->get();

            $export = new ExportCoupons($coupons->toArray());

            return Excel::download($export, 'Kupon Listesi.xlsx');
        }

        $coupons = $coupons_query->paginate(30)->withQueryString();
        
        $groups = CouponGroup::all();

        return Inertia('Coupon/Index',[
            'coupons' => $coupons,
            'groups' => $groups,
            'product_types' => ProductType::withoutGlobalScope(LanguageScope::class)->select('id','name','uuid','language')->get(),
            'filters' => [
                'coupon_group_id' => [
                    'label' => 'Grup',
                    'type' => 'select',
                    'options' => $groups->map(function($group){
                        return [
                            'label' => $group->name,
                            'value'=> (string)$group->id // query params string olarak aliyor. vue multiselect de eslesmesi icin string
                        ];
                    }),
                    'value' => $request->filter['coupon_group_id'] ?? [],
                    'full_width' => true
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create',Coupon::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create',Coupon::class);

        $validation_data = [
            'type' => 'required',
            'discount' => 'required',
            'start_date' => ['required',new CheckDateFormat],
            'end_date' => ['required',new CheckDateFormat],
            'apply_cart' => 'required',
            'coupon_group_id' => 'required',
            'count' => 'required|min:1',
            'code' => [Rule::requiredIf($request->count == 1),'unique:coupons']
        ];

        $request->validate($validation_data);

        if( $request->count == 1 ){
            
            $request->merge([
                'code' => Str::replace(' ','',$request->code)
            ]);
            $coupon = Coupon::create($request->all());

            if( $request->product_types && !$request->apply_cart ){
                $coupon->product_types()->attach($request->product_types);
            }

        } else {

            for ($i = 0; $i < $request->count; $i++) {

                $request->merge([
                    'code' => generateCouponCode()
                ]);

                $coupon = Coupon::create($request->all());

                if( $request->product_types ){
                    $coupon->product_types()->attach($request->product_types);
                }

            }

        }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $this->authorize('viewAny',Coupon::class);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $this->authorize('update',Coupon::class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $this->authorize('update',Coupon::class);

        $validation_data = [
            'type' => 'required',
            'discount' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'apply_cart' => 'required',
            'coupon_group_id' => 'required',
        ];

        $request->validate($validation_data);

        $coupon->update($request->all());

        $coupon->product_types()->detach();

        if( $request->product_types && !$request->apply_cart){
            $coupon->product_types()->attach($request->product_types);
        }

        return back();
    }

    public function delete(Coupon $coupon)
    {
        $this->authorize('delete',Coupon::class);

        $coupon->delete();
        return back();
    }

    public function restore(Coupon $coupon)
    {
        $this->authorize('restore',Coupon::class);

        $coupon->restore();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $this->authorize('forceDelete',Coupon::class);
    }

    public function generate()
    {
        return response()->json([
            'code' => generateCouponCode()
        ]);
    }
}
