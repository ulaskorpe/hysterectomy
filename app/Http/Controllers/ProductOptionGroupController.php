<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductOptionGroup;

class ProductOptionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->wantsJson() ){

            return ProductOptionGroup::all();

        }

        $this->authorize('viewAny',ProductOptionGroup::class);

        return Inertia('ProductOptionGroup/Index',[
            'product_option_groups' => ProductOptionGroup::with(['product_types','options','connected_product_option_groups'])->paginate(30),
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
        $this->authorize('create',ProductOptionGroup::class);

        $request->validate([
            'name' => 'required|string|max:50|unique:product_option_groups',
        ]);

        //uuid dolu geldiyse, farkli dilden karsilik giriliyordur. ancak cop kutusunda karsilik dil secimi olabilir. yeni olusturtmayalim.
        if( !empty($request->uuid) ){
            $exists = ProductOptionGroup::withTrashed()->where('uuid',$request->uuid)->first();

            if($exists){
                return back()->with('flash',[
                    'type' => 'error',
                    'message' => 'Seçilen dil için bağlantılı bir içerik bulunuyor. Lütfen çöp kutusunu kontrol ederek içeriği geri alınız!'
                ]);
            }
        }


        $product_option_group = ProductOptionGroup::create($request->all());
        
        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Ürün seçenek grubu oluşturuldu.',
            'data' => $product_option_group
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductOptionGroup $productOptionGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductOptionGroup $productOptionGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductOptionGroup $productOptionGroup)
    {
        $this->authorize('update',ProductOptionGroup::class);

        if($request->slug){
            $request->merge([
                'slug' => Str::slug($request->slug)
            ]);
        }
        
        $request->validate([
            'name' => ['required','string', 'max:50','unique:product_option_groups,name,'.$productOptionGroup->id],
            'slug' => ['required','string','unique:product_option_groups,slug,'.$productOptionGroup->id],
        ]);

        $productOptionGroup->fill($request->all());
        $productOptionGroup->save();
        
        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Ürün seçenek grubu güncellendi.',
            'data' => $productOptionGroup
        ]);
    }


    public function delete(ProductOptionGroup $productOptionGroup)
    {
        $this->authorize('delete',ProductOptionGroup::class);

        $productOptionGroup->delete();
        return back();
    }

    public function restore(ProductOptionGroup $productOptionGroup)
    {
        $this->authorize('retore',ProductOptionGroup::class);

        $productOptionGroup->restore();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductOptionGroup $productOptionGroup)
    {
        $this->authorize('forceDelete',ProductOptionGroup::class);
    }
}
