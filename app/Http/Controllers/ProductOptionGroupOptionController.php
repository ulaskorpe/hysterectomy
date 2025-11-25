<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductOptionGroupOption;
use App\Models\ProductOptionGroupOptionValue;

class ProductOptionGroupOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
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
        $request->validate([
            'name' => 'required|string|max:50',
            'product_option_group_id' => 'required|numeric',
            'option_type' => 'required',
            'fe_visible' => 'required|boolean'
        ]);

        $option = ProductOptionGroupOption::create($request->all());

        if( !empty($request->values) ){

            foreach ($request->values as $key => $value) {

                if (!empty($value['name'])) {
                    ProductOptionGroupOptionValue::create([
                        'product_option_group_option_id' => $option->id,
                        'name' => $value['name'],
                        'color_value' => $value['color_value'],
                        'image_uri' => $value['image_uri'],
                        'language' => $request->language
                    ]);
                }
            }

        }

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Seçenek oluşturuldu.',
            'data' => $option->toArray()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductOptionGroupOption $productOptionGroupOption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductOptionGroupOption $productOptionGroupOption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductOptionGroupOption $productOptionGroupOption)
    {
        if($request->slug){
            $request->merge([
                'slug' => Str::slug($request->slug)
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:50',
            'product_option_group_id' => 'required|numeric',
            'option_type' => 'required',
            'fe_visible' => 'required|boolean'
        ]);

        $productOptionGroupOption->fill($request->all());
        
        $productOptionGroupOption->save();

        //simdi values kismi icin bir kac isimiz var. Once varolanlari guncelleyecegiz. Yeni gelenleri ekleyecegiz. gelen listesinde olmayan eskileri silecegiz.
        $values_arr = [];

        foreach ($request->values as $key => $value) {

            //id bilgisi geldiyse update
            if( !empty($value['id']) ){
                
                ProductOptionGroupOptionValue::where('product_option_group_option_id',$productOptionGroupOption->id)
                ->where('id',$value['id'])
                ->update([
                    'name' => $value['name'],
                    'color_value' => $value['color_value'],
                    'image_uri' => $value['image_uri'],
                ]);

                $values_arr[] = $value['id'];

            } else {

                //id bos geldi, name alani bos degilse ekleyelim.
                if (!empty($value['name'])) {
                    
                    $optionGroupOptionValue = ProductOptionGroupOptionValue::create([
                        'product_option_group_option_id' => $productOptionGroupOption->id,
                        'name' => $value['name'],
                        'color_value' => $value['color_value'],
                        'image_uri' => $value['image_uri'],
                        'language' => $productOptionGroupOption->language
                    ]);

                    $values_arr[] = $optionGroupOptionValue->id;
                }
                
            }
            
        }

        //simdi listede olmayanlari silelim.
        $delete_option_values = ProductOptionGroupOptionValue::where('product_option_group_option_id',$productOptionGroupOption->id)->whereNotIn('id',$values_arr)->delete();

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Seçenek güncellendi.',
            'data' => $productOptionGroupOption->toArray()
        ]);
    }


    public function delete(ProductOptionGroupOption $productOptionGroupOption)
    {
        $productOptionGroupOption->delete();
        return back();
    }


    public function restore(ProductOptionGroupOption $productOptionGroupOption)
    {
        $productOptionGroupOption->restore();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductOptionGroupOption $productOptionGroupOption)
    {
        //
    }

    public function reorder(Request $request)
    {
        ProductOptionGroupOption::setNewOrder($request->data);

        return response()->json([
            'status' => 'success'
        ]);
    }

}
