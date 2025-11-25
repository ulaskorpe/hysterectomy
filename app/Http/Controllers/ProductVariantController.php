<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Rules\CheckDateFormat;
use Illuminate\Validation\Rule;
use App\Models\ProductPriceDiscount;
use App\Rules\CheckHourMinuteFormat;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Product $product)
    {

        $product->loadMissing([
            'product_variants',
            'product_variants.main_image',
            'product_type:id,name,product_option_group_id',
            'product_type.option_group:id,name,stock_usage,has_own_price',
            'product_type.option_group.options',
            'product_type.option_group.options.option_values',
        ]);

        if ($request->user()->cannot('view-' . $product->product_type->uuid)) {
            abort(403);
        }

        return inertia('Product/Variants', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'additional' => $product->additional,
                'use_option_group' => $product->use_option_group,
                'product_type_id' => $product->product_type_id,
                'product_type' => [
                    'id' => $product->product_type->id,
                    'name' => $product->product_type->name,
                    'product_option_group_id' => $product->product_type->product_option_group_id,
                    'option_group' => $product->product_type->option_group ? [
                        'id' => $product->product_type->option_group->id,
                        'name' => $product->product_type->option_group->name,
                        'stock_usage' => $product->product_type->option_group->stock_usage,
                        'has_own_price' => $product->product_type->option_group->has_own_price,
                        'options' => $product->product_type->option_group->options ? $product->product_type->option_group->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'name' => $option->name,
                                'slug' => $option->slug,
                                'option_type' => $option->option_type,
                                'values' => $option->option_type == 'select' || $option->option_type == 'multiselect' || $option->option_type == 'radio' ? $option->option_values : [],
                                'value' => null,
                                'fe_visible' => $option->fe_visible,
                                'order' => $option->order_column,
                            ];
                        }) : null,
                    ] : null,
                ],
                'product_variants' => $product->product_variants,
            ]
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
    public function store(Request $request, Product $product)
    {
        $product_type = $product->product_type;
        $option_group = $product_type->option_group;

        if ($request->user()->cannot('create-' . $product->product_type->uuid)) {
            abort(403);
        }

        $validation_data = [
            'price' => Rule::requiredIf($option_group->has_own_price),
            'stock' => Rule::requiredIf($option_group->stock_usage),
            'discount_type' => Rule::requiredIf($request->has_discount),
            'discount_value' => Rule::requiredIf($request->has_discount),
            'option_values' => 'required|array',
        ];

        foreach ($option_group->options as $k => $option_row) {
            
            if( in_array($option_row['id'],$product->additional['options_for_use']) ){
                if($option_row['option_type'] == 'time'){
                    $validation_data['option_values.'.$k.'.value'] = ['required',new CheckHourMinuteFormat];
                } else if($option_row['option_type'] == 'date'){
                    $validation_data['option_values.'.$k.'.value'] = ['required',new CheckDateFormat];
                } else {
                    $validation_data['option_values.'.$k.'.value'] = 'required';
                }
            }

        }

        $request->validate($validation_data);

        $request->merge([
            'product_id' => $product->id,
            'product_option_group_id' => $option_group->id,
        ]);

        $variant = ProductVariant::create($request->all());

        if( $request->media_objects['mainImage'] ){
            $variant->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
        }

        if( $option_group->has_own_price ){

            $variant_price = new ProductPrice();
            $variant_price->price = $request->price;
            $variant_price->currency_id = 1;
            $variant_price->final_price = $request->price;
            $variant->price()->save($variant_price);

            if($variant->has_discount){

                $variant_discount = ProductPriceDiscount::create([
                    'discount_type' => $request->discount_type,
                    'value' => $request->discount_value,
                    'product_price_id' => $variant->price->id,
                ]);

                $variant_price->update([
                    'final_price' => $variant_discount->discount_type == 'percentage' ? $variant->price->price - ($variant_discount->discount_value * $variant->price->price / 100) : $variant->price->price - $variant_discount->discount_value
                ]);
            }

        }

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Varyasyon oluşturuldu.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductVariant $productVariant)
    {
        $product_type = $product->product_type;
        $option_group = $product_type->option_group;

        if ($request->user()->cannot('edit-' . $product->product_type->uuid)) {
            abort(403);
        }

        $validation_data = [
            'price' => Rule::requiredIf($option_group->has_own_price),
            'stock' => Rule::requiredIf($option_group->stock_usage),
            'discount_type' => Rule::requiredIf($request->has_discount),
            'discount_value' => Rule::requiredIf($request->has_discount),
            'option_values' => 'required|array',
        ];

        foreach ($option_group->options as $k => $option_row) {
            
            if( in_array($option_row['id'],$product->additional['options_for_use']) ){
                if($option_row['option_type'] == 'time'){
                    $validation_data['option_values.'.$k.'.value'] = ['required',new CheckHourMinuteFormat];
                } else if($option_row['option_type'] == 'date'){
                    $validation_data['option_values.'.$k.'.value'] = ['required',new CheckDateFormat];
                } else {
                    $validation_data['option_values.'.$k.'.value'] = 'required';
                }
            }

        }

        $request->validate($validation_data);
        
        $request->merge([
            'product_id' => $product->id,
            'product_option_group_id' => $option_group->id,
        ]);

        $variant = ProductVariant::create($request->all());

        //siralamayi eskisine getir ve eskisini sil.
        ProductVariant::swapOrder($variant, $productVariant);
        $productVariant->delete();

        if( $request->media_objects['mainImage'] ){
            $variant->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
        }

        if( $option_group->has_own_price ){

            $variant_price = new ProductPrice();
            $variant_price->price = $request->price;
            $variant_price->currency_id = 1;
            $variant_price->final_price = $request->price;
            $variant->price()->save($variant_price);

            if($variant->has_discount){

                $variant_discount = ProductPriceDiscount::create([
                    'discount_type' => $request->discount_type,
                    'value' => $request->discount_value,
                    'product_price_id' => $variant->price->id,
                ]);

                $variant_price->update([
                    'final_price' => $variant_discount->discount_type == 'percentage' ? $variant->price->price - ($variant_discount->value * $variant->price->price / 100) : $variant->price->price - $variant_discount->discount_value
                ]);
            }

        }

        return redirect()->route('product-variants.index',$product->id)->with('flash',[
            'type' => 'success',
            'message' => 'Eski varyasyon silinerek yenisi oluşturuldu.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Product $product,ProductVariant $productVariant)
    {
        if ($request->user()->cannot('view-' . $product->product_type->uuid)) {
            abort(403);
        }

        $productVariant->delete();
        return back();
    }



    public function getReorder(Request $request, Product $product)
    {

        $product->loadMissing([
            'product_variants:id,option_values,product_id',
            'product_type:id,name,product_option_group_id',
            'product_type.option_group:id,name'
        ]);

        if ($request->user()->cannot('edit-' . $product->product_type->uuid)) {
            abort(403);
        }

        return Inertia('Product/ReOrderVariants', [
            'product' => $product
        ]);
    }

    public function reorder(Request $request, Product $product)
    {
        if ($request->user()->cannot('edit-' . $product->product_type->uuid)) {
            abort(403);
        }

        ProductVariant::setNewOrder($request->order_data, 10, null, function ($query) use ($product) {
            $query->where('product_id', $product->id);
        });

        return redirect()->route('product-variants.index', ['product' => $product]);
    }
}
