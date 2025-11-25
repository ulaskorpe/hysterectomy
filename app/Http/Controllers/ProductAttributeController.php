<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductType;
use App\Models\Scopes\LanguageScope;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->wantsJson() ){

            return ProductAttribute::all();

        }

        $this->authorize('viewAny',ProductAttribute::class);

        return Inertia('ProductAttribute/Index',[
            'product_attributes' => ProductAttribute::with(['product_types','values','connected_product_attributes'])->paginate(30),
            'product_types' => ProductType::withoutGlobalScope(LanguageScope::class)->select('id','name','uuid','language')->get(),
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
        $this->authorize('create',ProductAttribute::class);

        $request->validate([
            'name' => ['required','string', 'max:50'],
            'option_type' => 'required',
            'fe_visible' => 'required|boolean',
            'product_types' => 'required|array'
        ]);

        //uuid dolu geldiyse, farkli dilden karsilik giriliyordur. ancak cop kutusunda karsilik dil secimi olabilir. yeni olusturtmayalim.
        if( !empty($request->uuid) ){
            $exists = ProductAttribute::withTrashed()->where('uuid',$request->uuid)->first();

            if($exists){
                return back()->with('flash',[
                    'type' => 'error',
                    'message' => 'Seçilen dil için bağlantılı bir içerik bulunuyor. Lütfen çöp kutusunu kontrol ederek içeriği geri alınız!'
                ]);
            }
        }
        
        //unit_sclae geldiyse digerlerininkini false yapicaz.
        if( $request->unit_scale ){
            $disableUnitScale = ProductAttribute::where('unit_scale',true)->update(['unit_scale' => false]);
        }

        $attribute = ProductAttribute::create($request->all());
        $attribute->product_types()->attach($request->product_types);

        if( !empty($request->values) ){

            foreach ($request->values as $key => $value) {

                if (!empty($value['name'])) {
                    ProductAttributeValue::create([
                        'product_attribute_id' => $attribute->id,
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
            'data' => $attribute
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAttribute $productAttribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAttribute $productAttribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductAttribute $productAttribute)
    {

        $this->authorize('update',ProductAttribute::class);

        if($request->slug){
            $request->merge([
                'slug' => Str::slug($request->slug)
            ]);
        }
        
        $request->validate([
            'name' => ['required','string', 'max:50'],
            'option_type' => 'required',
            'fe_visible' => 'required|boolean',
            'product_types' => 'required|array'
        ]);

        //unit_sclae geldiyse digerlerininkini false yapicaz.
        if( $request->unit_scale ){
            $disableUnitScale = ProductAttribute::where('unit_scale',true)->where('id','!=',$productAttribute->id)->update(['unit_scale' => false]);
        }


        $productAttribute->fill($request->all());
        $productAttribute->save();

        $productAttribute->product_types()->sync($request->product_types);

        //simdi values kismi icin bir kac isimiz var. Once varolanlari guncelleyecegiz. Yeni gelenleri ekleyecegiz. gelen listesinde olmayan eskileri silecegiz.
        $values_arr = [];

        foreach ($request->values as $key => $value) {

            //id bilgisi geldiyse update
            if( !empty($value['id']) ){
                
                ProductAttributeValue::where('product_attribute_id',$productAttribute->id)
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
                    
                    $productAttributeValue = ProductAttributeValue::create([
                        'product_attribute_id' => $productAttribute->id,
                        'name' => $value['name'],
                        'color_value' => $value['color_value'],
                        'image_uri' => $value['image_uri'],
                        'language' => $productAttribute->language
                    ]);

                    $values_arr[] = $productAttributeValue->id;
                }
                
            }
            
        }

        //simdi listede olmayanlari silelim.
        $delete_option_values = ProductAttributeValue::where('product_attribute_id',$productAttribute->id)->whereNotIn('id',$values_arr)->get();
        $delete_option_values->each->delete();
        
        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Seçenek güncellendi.',
            'data' => $productAttribute
        ]);
    }


    public function delete(ProductAttribute $productAttribute)
    {
        $this->authorize('delete',ProductAttribute::class);

        $productAttribute->delete();
        return back();
    }

    public function restore(ProductAttribute $productAttribute)
    {
        $this->authorize('restore',ProductAttribute::class);

        $productAttribute->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAttribute $productAttribute)
    {
        //
    }
}
