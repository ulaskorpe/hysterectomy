<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCustomerAttribute;
use App\Models\ProductCustomerAttributeValue;
use App\Models\ProductType;
use App\Models\Scopes\LanguageScope;

class ProductCustomerAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->wantsJson() ){

            return ProductCustomerAttribute::all();

        }

        $this->authorize('viewAny',ProductCustomerAttribute::class);

        return Inertia('ProductCustomerAttribute/Index',[
            'product_customer_attributes' => ProductCustomerAttribute::with(['product_types','values','connected_product_customer_attributes'])->paginate(30),
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
        $this->authorize('create',ProductCustomerAttribute::class);

        $request->validate([
            'name' => ['required','string', 'max:50'],
            'option_type' => 'required',
            'fe_visible' => 'required|boolean',
            'product_types' => 'required|array'
        ]);

        //uuid dolu geldiyse, farkli dilden karsilik giriliyordur. ancak cop kutusunda karsilik dil secimi olabilir. yeni olusturtmayalim.
        if( !empty($request->uuid) ){
            $exists = ProductCustomerAttribute::withTrashed()->where('uuid',$request->uuid)->first();

            if($exists){
                return back()->with('flash',[
                    'type' => 'error',
                    'message' => 'Seçilen dil için bağlantılı bir içerik bulunuyor. Lütfen çöp kutusunu kontrol ederek içeriği geri alınız!'
                ]);
            }
        }
        

        $attribute = ProductCustomerAttribute::create($request->all());
        $attribute->product_types()->attach($request->product_types);

        if( !empty($request->values) ){

            foreach ($request->values as $key => $value) {

                if (!empty($value['name'])) {
                    ProductCustomerAttributeValue::create([
                        'product_customer_attribute_id' => $attribute->id,
                        'name' => $value['name'],
                        'color_value' => $value['color_value'],
                        'image_uri' => $value['image_uri'],
                        'language' => $request->language
                    ]);
                }
            }

        }
        
        return redirect()->route('product-customer-attributes.index')->with('flash',[
            'type' => 'success',
            'message' => 'Seçenek oluşturuldu.',
            'data' => $attribute
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCustomerAttribute $productCustomerAttribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCustomerAttribute $productCustomerAttribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCustomerAttribute $productCustomerAttribute)
    {

        $this->authorize('update',ProductCustomerAttribute::class);

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
        
        $productCustomerAttribute->fill($request->all());
        $productCustomerAttribute->save();

        $productCustomerAttribute->product_types()->sync($request->product_types);

        //simdi values kismi icin bir kac isimiz var. Once varolanlari guncelleyecegiz. Yeni gelenleri ekleyecegiz. gelen listesinde olmayan eskileri silecegiz.
        $values_arr = [];

        foreach ($request->values as $key => $value) {

            //id bilgisi geldiyse update
            if( !empty($value['id']) ){
                
                ProductCustomerAttributeValue::where('product_customer_attribute_id',$productCustomerAttribute->id)
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
                    
                    $productCustomerAttributeValue = ProductCustomerAttributeValue::create([
                        'product_customer_attribute_id' => $productCustomerAttribute->id,
                        'name' => $value['name'],
                        'color_value' => $value['color_value'],
                        'image_uri' => $value['image_uri'],
                        'language' => $productCustomerAttribute->language
                    ]);

                    $values_arr[] = $productCustomerAttributeValue->id;
                }
                
            }
            
        }

        //simdi listede olmayanlari silelim.
        $delete_option_values = ProductCustomerAttributeValue::where('product_customer_attribute_id',$productCustomerAttribute->id)->whereNotIn('id',$values_arr)->get();
        $delete_option_values->each->delete();
        
        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Seçenek güncellendi.',
            'data' => $productCustomerAttribute
        ]);
    }


    public function delete(ProductCustomerAttribute $productCustomerAttribute)
    {
        $this->authorize('delete',ProductCustomerAttribute::class);

        $productCustomerAttribute->delete();
        return back();
    }

    public function restore(ProductCustomerAttribute $productCustomerAttribute)
    {
        $this->authorize('restore',ProductCustomerAttribute::class);

        $productCustomerAttribute->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCustomerAttribute $productCustomerAttribute)
    {
        //
    }
}
