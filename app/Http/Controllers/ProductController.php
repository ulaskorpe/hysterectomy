<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Support\Arr;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Rules\CheckDateFormat;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPriceDiscount;
use App\Models\Scopes\LanguageScope;
use App\Rules\CheckHourMinuteFormat;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\ProductOptionGroupOption;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    
    protected $productType;

    public function __construct(Request $request, ProductType $productType)
    {

        $productType = ProductType::withoutGlobalScope(LanguageScope::class)->where('id', $request->productType)->first();

        $this->productType = $productType;

    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('view-'.$this->productType->uuid)) {
            abort(403);
        }

        //simdi once ing karsilik secilmis olabilir. productType karsi dildeki olacak.
        if( $this->productType && $this->productType->language != $request->language ){
            
            //ilgili dildeki contenti alalim. gelen dil direkt scope da var.
            $connectedProductType = ProductType::where('uuid',$this->productType->uuid)->first();

            if( !$connectedProductType ){
                return back()->with('flash',[
                    'type' => 'error',
                    'message' => 'Ürün tipi için seçilen dilde içerik bulunmuyor. Öncelikle içerik tipleri menüsünden ürün tipini oluşturun!'
                ]);
            }

            return redirect()->route('products.index',[
                'productType' => $connectedProductType->id,
                'language' => $connectedProductType->language
            ]);
        }


        if( !$this->productType ){
            return redirect()->route('dashboard')->with('flash',[
                'type' => 'error',
                'message' => 'Ürün tipi bulunamadı.'
            ]);
        }

        if($request->wantsJson()){
            $products = Product::select('id','name','uuid','language')->where('product_type_id',$this->productType->id)->ordered()->paginate(30);
            return $products;
        };

        $search = $request->search;

        $defaultSort = 'order_column';

        $products = QueryBuilder::for(Product::class)
        ->select('id','name','order_column','slug','sku','uuid','language','featured','order_count','use_option_group')
        ->with(['uri','connected_products','main_image'])
        ->where('product_type_id',$this->productType->id)
        ->when($search && !empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name','like','%'.$search.'%')->orWhere('summary','like','%'.$search.'%')->orWhere('sku','like','%'.$search.'%');
            });
        })
        ->defaultSort($defaultSort)
        ->allowedSorts('name','id','order_column','start_date','end_date','created_at','updated_at','order_count')
        ->allowedFilters([
            AllowedFilter::callback('categories', function (Builder $query, $value) {
                $value = array_map('intval', $value);
                $query->whereHas('product_categories',function (Builder $qqq) use ($value) {
                    $qqq->whereIn('id',$value);
                });
            }),
        ])
        ->paginate(30)->withQueryString();
        
        return Inertia('Product/Index',[
            'products' => $products,
            'product_type' => $this->productType,
            'filters' => [
                'categories' => [
                    'label' => 'Kategori',
                    'type' => 'select',
                    'options' => ProductCategory::select('id','name')->where('product_type_id',$this->productType->id)->get()->map(function($st){
                        return [
                            'label' => $st->name,
                            'value'=> (string)$st->id
                        ];
                    }),
                    'value' => $request->filter['categories'] ?? [],
                    'full_width' => true
                ]
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if( !$this->productType ){
            return redirect()->route('dashboard')->with('flash',[
                'type' => 'error',
                'message' => 'Ürün tipi bulunamadı.'
            ]);
        }

        if ($request->user()->cannot('create-'.$this->productType->uuid)) {
            abort(403);
        }
        
        $copy_content = null;

        if( $request->has('uuid') && $request->has('from_id') ){
            
            //once kayit varmi bakalim.
            $exists = Product::withTrashed()->where('uuid',$request->uuid)->first(); //scope var language direkt aliyor.
            if( $exists ){
                return back()->with('flash',[
                    'type' => 'error',
                    'message' => 'Seçilen dil için bağlantılı bir içerik bulunuyor. Çöp kutusunu kontrol ederek içeriği geri alabilirsiniz!'
                ]);
            }

            $copy_content_model = Product::withoutGlobalScope(LanguageScope::class)
            ->with(['product_type' => function($pp){
                $pp->select('id','uuid','name','language')->withoutGlobalScope(LanguageScope::class);
            }])
            ->where('id',$request->from_id)->first();

            if($copy_content_model){
                
                //simdi talep ilk gediginden productType tr kaliyor ornegin. Bunu request icinde productType EN olana redirect etmemiz lazim.
                if( $this->productType && $this->productType->language != $request->language ){

                    $from_product_type = ProductType::select('id','uuid','name','language')->where('uuid',$copy_content_model->product_type->uuid)->first();

                    if( !$from_product_type ){
                        return back()->with('flash',[
                            'type' => 'error',
                            'message' => 'Seçilen dil için, ürüne ait ürün tipi oluşturulmamış. Öncelikle ürün tipini oluşturun!'
                        ]);
                    }

                    return redirect()->route('products.create',[
                        'productType' => $from_product_type->id,
                        'language' => $from_product_type->language,
                        'uuid' => $request->uuid,
                        'from_id' => $request->from_id
                    ]);
                }

                $copy_content_model->media_objects = [
                    'mainImage' => $copy_content_model->library_media_group['mainImage'][0] ?? null,
                    'headerImage' => $copy_content_model->library_media_group['headerImage'][0] ?? null,
                    'mainVideo' => $copy_content_model->library_media_group['mainVideo'][0] ?? null,
                    'headerVideo' => $copy_content_model->library_media_group['headerVideo'][0] ?? null,
                    'galleryImages' => $copy_content_model->library_media_group['galleryImages'] ?? null,
                    'mainFile' => $copy_content_model->library_media_group['mainFile'][0] ?? null,
                ];
                $copy_content_model->name = $copy_content_model->name.' - '.strtoupper($this->productType->language);
                $copy_content = $copy_content_model;
                
            }

        }

        if( $request->has('copy_id') ){
            
            //once kayit varmi bakalim.
            $exists = Product::find($request->copy_id); //scope var language direkt aliyor.
            if( $exists ){

                $exists->media_objects = [
                    'mainImage' => $exists->library_media_group['mainImage'][0] ?? null,
                    'headerImage' => $exists->library_media_group['headerImage'][0] ?? null,
                    'mainVideo' => $exists->library_media_group['mainVideo'][0] ?? null,
                    'headerVideo' => $exists->library_media_group['headerVideo'][0] ?? null,
                    'galleryImages' => $exists->library_media_group['galleryImages'] ?? null,
                    'mainFile' => $exists->library_media_group['mainFile'][0] ?? null,
                ];
                $exists->name = $exists->name.' - KOPYA';

                $copy_content = $exists;
            }

        }

        $categories = ProductCategory::where('product_type_id',$this->productType->id)->get();

        //inertisa formda kullanmak icin options arrayi burada olusturalim.
        $option_group = null;
        $product_attributes = [];

        if( $this->productType->option_group ){

            $group = $this->productType->option_group;

            $data = [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'has_own_price' => $group->has_own_price,
                'stock_usage' => $group->stock_usage,
                'options' => []
            ];

            $option_row = [
                'stock' => null,
                'price' => null,
                'has_discount' => false,
                'discount_type' => 'percentage',
                'discount_value' => 0,
                'row_options' => [],
                'media_objects' => [
                    'mainImage' => null,
                    'headerImage' => null,
                    'mainVideo' => null,
                    'headerImage' => null,
                    'galleryImages' => [],
                ]
            ];

            foreach ($group->options as $k => $option) {
                
                $option_row['row_options'][$k] = [
                    'id' => $option->id,
                    'name' => $option->name,
                    'slug' => $option->slug,
                    'option_type' => $option->option_type,
                    'values' => $option->option_type == 'select' || $option->option_type == 'multiselect' || $option->option_type == 'radio' ? $option->option_values : [],
                    'value' => null,
                    'fe_visible' => $option->fe_visible,
                    'order' => $option->order_column,
                ];

            }

            $data['options'][] = $option_row; //bunu row olarak atiyorum ki vue tarafinda cogaltilabilsin.

            $option_group = $data;

        }

        if( $this->productType->product_attributes ){

            foreach ($this->productType->product_attributes as $key => $attribute) {
                $data = [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'option_type' => $attribute->option_type,
                    'values' => in_array($attribute->option_type,['select','multiselect','radio']) ? $attribute->values : [],
                    'value' => null,
                    'fe_visible' => $attribute->fe_visible,
                    'unit_scale' => $attribute->unit_scale ?? false
                ];

                $product_attributes[] = $data;

            }

        }

        return Inertia('Product/Create',[
            'product_type' => $this->productType,
            'option_group' => $option_group,
            'product_attributes' => $product_attributes,
            'categories' => sortArrayByParentId($categories->toArray()),
            'copy_content' => $copy_content,
            'taxes' => Tax::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Store request kullanmiyorum. ozel isler var.
     */
    public function store(Request $request)
    {   
        
        if( !$this->productType ){
            return back()->with('flash',[
                'type' => 'error',
                'message' => 'Ürün tipi bulunamadı.'
            ]);
        }

        if ($request->user()->cannot('create-'.$this->productType->uuid)) {
            abort(403);
        }

        $categories_count = ProductCategory::where('product_type_id',$this->productType->id)->count();

        $validation_data = [
            'name' => 'required|string|max:190',
            'language' => 'required|string|max:3',
            'sku' => 'required|unique:products',
            'price' => Rule::requiredIf($this->productType->is_cartable),
            'stock' => Rule::requiredIf($this->productType->stock_usage),
            'product_categories' => [Rule::requiredIf($categories_count > 0)],
            'discount_type' => Rule::requiredIf($request->has_discount),
            'discount_value' => Rule::requiredIf($request->has_discount),
            'seo.title' => 'required|string',
            'seo.description' => 'required|string',
        ];

        if( $this->productType->option_group && $request->use_option_group ){

            $group = $this->productType->option_group;
            $validation_data['option_group'] = 'required|array';

            foreach ($request->option_group['options'] as $o => $options) {
                    
                $validation_data['option_group.options.'.$o.'.price'] = Rule::requiredIf($group['has_own_price']);
                $validation_data['option_group.options.'.$o.'.stock'] = Rule::requiredIf($group['stock_usage']);

                foreach ($options['row_options'] as $k => $option_row) {
                
                    if( in_array($option_row['id'],$request->additional['options_for_use']) ){
                        if($option_row['option_type'] == 'time'){
                            $validation_data['option_group.options.'.$o.'.row_options.'.$k.'.value'] = ['required',new CheckHourMinuteFormat];
                        } else if($option_row['option_type'] == 'date'){
                            $validation_data['option_group.options.'.$o.'.row_options.'.$k.'.value'] = ['required',new CheckDateFormat];
                        } else {
                            $validation_data['option_group.options.'.$o.'.row_options.'.$k.'.value'] = 'required';
                        }
                    }

                }

            }

        }

        if( count($this->productType->product_attributes) > 0 ){

            foreach ($this->productType->product_attributes as $key => $attribute) {

                if($attribute['option_type'] == 'time'){
                    $validation_data['attributes.'.$key.'.value'] = [Rule::requiredIf($attribute['is_required']),new CheckHourMinuteFormat];
                } else if($attribute['option_type'] == 'date'){
                    $validation_data['attributes.'.$key.'.value'] = [Rule::requiredIf($attribute['is_required']),new CheckDateFormat];
                } else {
                    $validation_data['attributes.'.$key.'.value'] = Rule::requiredIf($attribute['is_required']);
                }
            }

        }

        $request->validate($validation_data);

        $request->merge([
            'product_type_id' => $this->productType->id,
        ]);

        $product = Product::create($request->all());

        if( $request->product_categories ){
            $product->product_categories()->attach($request->product_categories);
        }

        if( $this->productType->is_cartable ){

            $price = new ProductPrice();
            $price->price = $request->price;
            $price->currency_id = 1;
            $price->final_price = $request->price;
            $product->product_price()->save($price);

            if($product->has_discount){
                $price_discount = ProductPriceDiscount::create([
                    'discount_type' => $request->discount_type,
                    'value' => $request->discount_value,
                    'product_price_id' => $price->id,
                ]);
                $price->update([
                    'final_price' => $request->discount_type == 'percentage' ? $price->price - ($request->discount_value * $price->price / 100) : $price->price - $request->discount_value
                ]);
            }
        }

        if ($request->media_objects) {
            if( $request->media_objects['mainImage'] ){
                $product->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
            }
            if( $request->media_objects['headerImage'] ){
                $product->library_media()->attach($request->media_objects['headerImage']['id'],['collection' => 'headerImage']);
            }
            if( $request->media_objects['mainVideo'] ){
                $product->library_media()->attach($request->media_objects['mainVideo']['id'],['collection' => 'mainVideo']);
            }
            if( $request->media_objects['galleryImages'] ){
                $product->library_media()->attach(Arr::pluck($request->media_objects['galleryImages'],'id'),['collection' => 'galleryImages']);
            }
        }
        
        //option grouplar geldiyse variantlari olusturalim.
        if( $request->option_group && $request->use_option_group){
            
            $group = $request->option_group;

            foreach ($group['options'] as $k => $option) {
                

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'product_option_group_id' => $group['id'],
                    'option_values' => $option['row_options'],
                    'has_discount' => $option['has_discount'],
                ]);

                if( $option['media_objects']['mainImage'] ){
                    $variant->library_media()->attach($option['media_objects']['mainImage']['id'],['collection' => 'mainImage']);
                }

                if( $group['has_own_price'] ){

                    $variant_price = new ProductPrice();
                    $variant_price->price = $option['price'];
                    $variant_price->currency_id = 1;
                    $variant_price->final_price = $option['price'];
                    $variant->price()->save($variant_price);

                    if($option['has_discount']){

                        $variant_discount = ProductPriceDiscount::create([
                            'discount_type' => $option['discount_type'],
                            'value' => $option['discount_value'],
                            'product_price_id' => $variant->price->id,
                        ]);

                        $variant_price->update([
                            'final_price' => $option['discount_type'] == 'percentage' ? $variant->price->price - ($option['discount_value'] * $variant->price->price / 100) : $variant->price->price - $option['discount_value']
                        ]);
                    }

                }

                if( $group['stock_usage'] ){

                    $variant->update(['stock' => $option['stock']]);

                }

            }

        }

        //breadcrumb
        $bc = saveBreadCrumb($product);

        return redirect()->route('products.index',['productType' => $this->productType->id, 'language' => $product->language])->with('flash',[
            'type' => 'success',
            'message' => $this->productType->name.' oluşturuldu.',
            'data' => $product
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Product $product)
    {
        if ($request->user()->cannot('view-'.$product->product_type->uuid)) {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Product $product)
    {

        if ($request->user()->cannot('edit-'.$product->product_type->uuid)) {
            abort(403);
        }

        $categories = ProductCategory::select('id','name','language','uuid','parent_id')->where('product_type_id',$product->product_type_id)->get();

        $option_group = null;
        $product_attributes = [];

        $productForUse = Product::where('id',$product->id)->with([
            'product_type:id,product_option_group_id,name,language,uuid,stock_usage,is_cartable,is_contactable,after_order_type',
            'product_type.option_group',
            'product_type.product_attributes',
            'product_variants:id,product_id,product_option_group_id,option_values,additional,stock,has_discount',
            'product_variants.price',
            'product_variants.main_image',
            'library_media',
            'tags:id,name',
            'product_categories:id,name',
            'product_price'
        ])->first();

        $productForUse->media_objects = [
            'mainImage' => $productForUse->library_media_group['mainImage'][0] ?? null,
            'headerImage' => $productForUse->library_media_group['headerImage'][0] ?? null,
            'mainVideo' => $productForUse->library_media_group['mainVideo'][0] ?? null,
            'headerVideo' => $productForUse->library_media_group['headerVideo'][0] ?? null,
            'galleryImages' => $productForUse->library_media_group['galleryImages'] ?? null,
            'mainFile' => $productForUse->library_media_group['mainFile'][0] ?? null,
        ];

        $productForUse->categories = $productForUse->product_categories;
        $productForUse->price = $productForUse->product_price;


        if( $productForUse->product_type->option_group ){

            $group = $productForUse->product_type->option_group;
            
            $data = [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'has_own_price' => $group->has_own_price,
                'stock_usage' => $group->stock_usage,
                'options' => [],
            ];
            
            $currents = $productForUse->product_variants->filter(function ($item) use ($group) {
                return $item->product_option_group_id === $group->id;
            });


            $productOptionGroupOptions = ProductOptionGroupOption::select('id','name','option_type')->with('option_values')->get();
            
            if( $currents->count() > 0 ){

                //yeni eklenen option olabilir. bunlari da bos gonderelim.
                $added_options = [];

                foreach ($group->options as $k => $option) {
                
                    $exists_in_current = $currents->filter(function ($item) use ($option) {
                        return collect($item->option_values)->contains('id', $option->id);
                    })->first();
                    

                    if(!$exists_in_current){
                        $added_options[] = [
                            'id' => $option->id,
                            'name' => $option->name,
                            'slug' => $option->slug,
                            'option_type' => $option->option_type,
                            'values' => $option->option_type == 'select' || $option->option_type == 'multiselect' || $option->option_type == 'radio' ? $option->option_values : [],
                            'value' => null,
                            'fe_visible' => $option->fe_visible,
                            'order' => $option->order_column,
                        ];
                    }

                }

                foreach ($currents as $current) {

                    $current_values = $current->option_values;
                    
                    //mevcuttakinin option_type i degismis olabilir. guncelleyelim.
                    foreach ($current_values as $v => $value) {

                        $get_option_type = $productOptionGroupOptions->filter(function ($item) use ($value) {
                            return $item->id === $value['id'];
                        })->first();
                        
                        //option type degistiyse value kismini resetleyelim. silindiyse kaldiralim.
                        
                        if( !$get_option_type ){
                            unset($current_values[$v]);
                        } else {
                            if( $current_values[$v]['option_type'] != $get_option_type->option_type ){
                                $current_values[$v]['value'] = null;
                            }
                            $current_values[$v]['name'] = $get_option_type->name;
                            $current_values[$v]['option_type'] = $get_option_type->option_type;
                            $current_values[$v]['values'] = $get_option_type->option_values->toArray();
                        }
                        
                    }

                    if( count($added_options) > 0 ){
                        foreach ($added_options as $a => $added) {
                            $current_values[] = $added;
                        }
                    }

                    foreach ($current_values as $vv => $value) {
                        $current_values[$vv]['value'] = formatOptionType($value['value'],$value['option_type']);
                    }

                    $sorted_current_values = array_values(Arr::sort($current_values, function (array $value) {
                        return $value['order'] ?? false;
                    }));

                    $data['options'][] = [
                        'stock' => $current->stock,
                        'price' => $current->price ? $current->price->price : null,
                        'has_discount' => $current->has_discount,
                        'discount_type' => $current->price && $current->price->discount ? $current->price->discount->discount_type : 'percentage',
                        'discount_value' => $current->price && $current->price->discount ? $current->price->discount->value : 0,
                        'row_options' => $sorted_current_values,
                        'media_objects' => [
                            'mainImage' => $current->library_media_group['mainImage'][0] ?? null,
                            'headerImage' => null,
                            'mainVideo' => null,
                            'galleryImages' => [],
                            'mainFile' => null
                        ]
                    ];

                }
                

            } else {

                $option_row = [
                    'stock' => null,
                    'price' => null,
                    'has_discount' => false,
                    'discount_type' => 'percentage',
                    'discount_value' => 0,
                    'row_options' => [],
                    'media_objects' => [
                        'mainImage' => null,
                        'headerImage' => null,
                        'mainVideo' => null,
                        'headerImage' => null,
                        'galleryImages' => [],
                        'mainFile' => null
                    ]
                ];

                foreach ($group->options as $k => $option) {
                
                    $option_row['row_options'][] = [
                        'id' => $option->id,
                        'name' => $option->name,
                        'slug' => $option->slug,
                        'option_type' => $option->option_type,
                        'values' => $option->option_type == 'select' || $option->option_type == 'multiselect' || $option->option_type == 'radio' ? $option->option_values : [],
                        'value' => null,
                        'fe_visible' => $option->fe_visible,
                        'order' => $option->order_column,
                    ];

                }

                $data['options'][] = $option_row;

            }

            $option_group = $data;

        }
        

        if( $productForUse->product_type->product_attributes ){

            foreach ($productForUse->product_type->product_attributes as $key => $attribute) {
                
                $current = null;

                if( $productForUse->attributes ){
                    $current = Arr::first($productForUse->attributes, function ($value, $key) use ($attribute) {
                        return $value['id'] == $attribute->id;
                    });
                }

                $current_value = null;

                if($current){

                    //mevcuttakinin option_type i degismis olabilir. guncelleyelim.
                    $get_option_type = ProductAttribute::find($current['id']);
                    //option type degismediyse value basalim.
                    if( $get_option_type->option_type == $current['option_type'] ){
                        $current_value = formatOptionType($current['value'],$current['option_type']);
                    }

                }

                $data = [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'option_type' => $attribute->option_type,
                    'values' => in_array($attribute->option_type,['select','multiselect']) ? $attribute->values : [],
                    'value' => $current_value,
                    'fe_visible' => $attribute->fe_visible,
                    'unit_scale' => $attribute->unit_scale ?? false
                ];

                $product_attributes[] = $data;

            }

        }


        return Inertia('Product/Edit',[
            'product' => $productForUse, //variant cok oldugunda her biri icin main image felan epey query calisiyor. bubna bir bakmak gerekebilir.
            'product_type' => $productForUse->product_type,
            'option_group' => $option_group,
            'product_attributes' => $product_attributes,
            'categories' => sortArrayByParentId($categories->toArray()),
            'taxes' => Tax::all()
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        if ($request->user()->cannot('edit-'.$product->product_type->uuid)) {
            abort(403);
        }

        $categories_count = ProductCategory::where('product_type_id',$product->product_type_id)->count();

        $validation_data = [
            'name' => 'required|string|max:190',
            'language' => 'required|string|max:3',
            'sku' => 'required|unique:products,sku,'.$product->id,
            //'slug' => ['required','string','unique:products,slug,'.$product->id],
            'price' => Rule::requiredIf($product->product_type->is_cartable),
            'stock' => Rule::requiredIf($product->product_type->stock_usage),
            'product_categories' => [Rule::requiredIf($categories_count > 0)],
            'attributes.*.value' => Rule::requiredIf(count($product->product_type->product_attributes) > 0),
            'discount_type' => Rule::requiredIf($request->has_discount),
            'discount_value' => Rule::requiredIf($request->has_discount),
            'seo.title' => 'required|string',
            'seo.description' => 'required|string',
        ];


        if( $product->product_type->option_group && $request->use_option_group ){

            $group = $product->product_type->option_group;
            $validation_data['option_group'] = 'required|array';

            foreach ($request->option_group['options'] as $o => $options) {
                    
                $validation_data['option_group.options.'.$o.'.price'] = Rule::requiredIf($group['has_own_price']);
                $validation_data['option_group.options.'.$o.'.stock'] = Rule::requiredIf($group['stock_usage']);

                foreach ($options['row_options'] as $k => $option_row) {
                
                    if( in_array($option_row['id'],$request->additional['options_for_use']) ){
                        if($option_row['option_type'] == 'time'){
                            $validation_data['option_group.options.'.$o.'.row_options.'.$k.'.value'] = ['required',new CheckHourMinuteFormat];
                        } else if($option_row['option_type'] == 'date'){
                            $validation_data['option_group.options.'.$o.'.row_options.'.$k.'.value'] = ['required',new CheckDateFormat];
                        } else {
                            $validation_data['option_group.options.'.$o.'.row_options.'.$k.'.value'] = 'required';
                        }
                    }

                }

            }

        }

        if( count($product->product_type->product_attributes) > 0 ){

            foreach ($product->product_type->product_attributes as $key => $attribute) {

                if($attribute['option_type'] == 'time'){
                    $validation_data['attributes.'.$key.'.value'] = [Rule::requiredIf($attribute['is_required']),new CheckHourMinuteFormat];
                } else if($attribute['option_type'] == 'date'){
                    $validation_data['attributes.'.$key.'.value'] = [Rule::requiredIf($attribute['is_required']),new CheckDateFormat];
                } else {
                    $validation_data['attributes.'.$key.'.value'] = Rule::requiredIf($attribute['is_required']);
                }
            }

        }

        $request->validate($validation_data);

        $request->merge([
            'product_type_id' => $product->product_type->id,
        ]);

        $product->update($request->all());

        $product->product_categories()->detach();
        $product->library_media()->detach();

        if( $request->product_categories ){
            $product->product_categories()->attach($request->product_categories);
        }

        if( $product->product_type->is_cartable ){

            if($product->product_price){

                $product->product_price()->update([
                    'price' => $request->price,
                    'final_price' => $request->price,
                ]);

            } else {

                $price = new ProductPrice();
                $price->price = $request->price;
                $price->currency_id = 1;
                $price->final_price = $request->price;
                $product->product_price()->save($price);

            }

            if($product->has_discount){
                $price_discount = ProductPriceDiscount::updateOrCreate(
                    [
                        'product_price_id' => $product->product_price->id
                    ],
                    [
                        'discount_type' => $request->discount_type,
                        'value' => $request->discount_value,
                        'product_price_id' => $product->product_price->id,
                    ]
                );
                $product->product_price()->update([
                    'final_price' => $request->discount_type == 'percentage' ? $product->product_price->price - ($request->discount_value * $product->product_price->price / 100) : $product->product_price->price - $request->discount_value
                ]);
            }
        }

        if ($request->media_objects) {
            if( $request->media_objects['mainImage'] ){
                $product->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
            }
            if( $request->media_objects['headerImage'] ){
                $product->library_media()->attach($request->media_objects['headerImage']['id'],['collection' => 'headerImage']);
            }
            if( $request->media_objects['mainVideo'] ){
                $product->library_media()->attach($request->media_objects['mainVideo']['id'],['collection' => 'mainVideo']);
            }
            if( $request->media_objects['galleryImages'] ){
                $product->library_media()->attach(Arr::pluck($request->media_objects['galleryImages'],'id'),['collection' => 'galleryImages']);
            }
        }
        

        //option grouplar geldiyse variantlari olusturalim.
        $delete_old_variants = ProductVariant::where('product_id',$product->id)->delete();

        if( $request->option_group && $request->use_option_group ){
            
            $group = $request->option_group;

            foreach ($group['options'] as $k => $option) {
                    
                /*
                foreach ($option['row_options'] as $o => $value) {
                    if( $value['option_type'] == 'time' ){
                        $option['row_options'][$o]['value'] = Carbon::parse($value['value'])->addHours(3)->format('H:i');
                    }
                }
                */

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'product_option_group_id' => $group['id'],
                    'option_values' => $option['row_options'],
                    'has_discount' => $option['has_discount'],
                ]);

                if( $option['media_objects']['mainImage'] ){
                    $variant->library_media()->attach($option['media_objects']['mainImage']['id'],['collection' => 'mainImage']);
                }

                if( $group['has_own_price'] ){

                    $variant_price = new ProductPrice();
                    $variant_price->price = $option['price'];
                    $variant_price->currency_id = 1;
                    $variant_price->final_price = $option['price'];
                    $variant->price()->save($variant_price);

                    if($option['has_discount']){
                        $variant_discount = ProductPriceDiscount::updateOrCreate(
                            [
                                'product_price_id' => $variant->price->id
                            ],
                            [
                                'discount_type' => $option['discount_type'],
                                'value' => $option['discount_value'],
                                'product_price_id' => $variant->price->id,
                            ]
                        );
                        $variant_price->update([
                            'final_price' => $option['discount_type'] == 'percentage' ? $variant->price->price - ($option['discount_value'] * $variant->price->price / 100) : $variant->price->price - $option['discount_value']
                        ]);
                    }

                }

                if( $group['stock_usage'] ){

                    $variant->update(['stock' => $option['stock']]);

                }

            }

        }

        //breadcrumb
        $bc = saveBreadCrumb($product);

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Ürün güncellendi.',
            'data' => $product
        ]);
    }



    public function delete(Request $request, Product $product)
    {
        if ($request->user()->cannot('delete-'.$product->product_type->uuid)) {
            abort(403);
        }

        $product->delete();
        return back();
    }

    public function restore(Request $request, Product $product)
    {
        if ($request->user()->cannot('delete-'.$product->product_type->uuid)) {
            abort(403);
        }

        $product->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        if ($request->user()->cannot('delete-'.$product->product_type->uuid)) {
            abort(403);
        }
    }


    public function getReorder(Request $request)
    {

        if( !$this->productType ){
            return response()->json([
                'status' => 'error',
                'message' => 'Ürün tipi bulunamadı!'
            ]);
        }

        if ($request->user()->cannot('view-'.$this->productType->uuid)) {
            abort(403);
        }

        //normal cagirdigimizda bissuru relation cagirdigi icin DB ile cagiriyorum.
        $products = DB::table('products')->where('product_type_id',$this->productType->id)
        ->orderBy('order_column', 'ASC')
        ->get(['id','name']);
        
        return Inertia('Product/ReOrder',[
            'products' => $products,
            'product_type' => $this->productType,
        ]);
    }

    public function reorder(Request $request)
    {

        if( !$this->productType ){
            return response()->json([
                'status' => 'error',
                'message' => 'Ürün tipi bulunamadı!'
            ]);
        }

        Product::setNewOrder($request->order_data, 10, null, function($query) {
            $query->where('product_type_id',$this->productType->id);
        });

        return redirect()->route('products.index',['productType' => $this->productType->id]);
    }

    //BULK
    public function bulkDelete(Request $request)
    {
        if ($request->user()->cannot('delete-'.$this->productType->uuid)) {
            abort(403);
        }

        $products = Product::whereIn('id',$request->items)->delete();
        return back()->with('flash',[
            'type' =>'success',
            'message' => "Seçilen ürünler silindi!"
        ]);
    }

}
