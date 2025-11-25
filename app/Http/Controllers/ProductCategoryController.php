<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\Scopes\LanguageScope;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ProductCategoryController extends Controller
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

            return redirect()->route('product-categories.index',[
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
            $categories = ProductCategory::select('id','name','parent_id','product_type_id','language')
            ->get();

            return sortArrayByParentId($categories->toArray());
        };

        $categories = QueryBuilder::for(ProductCategory::class)
        ->select('id','name','parent_id','product_type_id','language','slug','is_published','summary','description','uuid','seo')
        ->with(['uri','connected_product_categories'])
        ->where('product_type_id',$this->productType->id)
        ->allowedFilters([
            AllowedFilter::exact('product_type_id'),
        ])
        ->get()
        ->map(function($cat){
            $cat->media_objects = $cat->media_objects;
            return $cat;
        });
        
        return Inertia('ProductCategory/Index',[
            'categories' => sortArrayByParentId($categories->toArray()),
            'product_type' => $this->productType->loadMissing('connected_product_types'),
            'filters' => [
                'product_type_id' => [
                    'label' => 'Ürün Tipi',
                    'type' => 'selectGroup',
                    'value' => $request->filter['product_type_id'] ?? [],
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
        //
    }

    /**
     * Store a newly created resource in storage.
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

        $request->validate([
            'name' => 'required|string|max:50',
            'language' => 'required|string|max:2',
            'product_type_id' => 'required|numeric',
            'seo.title' => 'required',
        ]);

        //uuid dolu geldiyse, farkli dilden karsilik giriliyordur. ancak cop kutusunda karsilik dil secimi olabilir. yeni olusturtmayalim.
        if( !empty($request->uuid) ){
            $exists = ProductCategory::withTrashed()->where('uuid',$request->uuid)->first();

            if($exists){
                return back()->with('flash',[
                    'type' => 'error',
                    'message' => 'Seçilen dil için bağlantılı bir içerik bulunuyor. Lütfen çöp kutusunu kontrol ederek içeriği geri alınız!'
                ]);
            }
        }

        $category = ProductCategory::create($request->all());

        if ($request->media_objects) {
            if( $request->media_objects['mainImage'] ){
                $category->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
            }
            if( $request->media_objects['headerImage'] ){
                $category->library_media()->attach($request->media_objects['headerImage']['id'],['collection' => 'headerImage']);
            }
            if( $request->media_objects['mainVideo'] ){
                $category->library_media()->attach($request->media_objects['mainVideo']['id'],['collection' => 'mainVideo']);
            }
            if( $request->media_objects['galleryImages'] ){
                $category->library_media()->attach(Arr::pluck($request->media_objects['galleryImages'],'id'),['collection' => 'galleryImages']);
            }
        }

        //breadcrumb
        $bc = saveBreadCrumb($category);
        
        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Ürün kategorisi oluşturuldu.',
            'data' => $category
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        if ($request->user()->cannot('edit-'.$this->productType->uuid)) {
            abort(403);
        }

        if($request->slug){
            $request->merge([
                'slug' => Str::slug($request->slug)
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:50',
            'language' => 'required|string|max:2',
            'product_type_id' => 'required|numeric',
            'seo.title' => 'required',
        ]);

        $current_parent_id = $productCategory->parent_id;

        $productCategory->fill($request->all());
        
        if ($productCategory->isDirty('parent_id')) {
            
            //eger parent_id degistiyse ve bu arkasin altinda bir yere geldiyse, normalde parent id si bu arkadas olan bir uste cikmali.
            $parent_degistir = ProductCategory::where('parent_id',$productCategory->id)->update([
                "parent_id" => $current_parent_id ?? null
            ]);

        }

        $productCategory->save();

        $productCategory->library_media()->detach();

        if ($request->media_objects) {
            if( $request->media_objects['mainImage'] ){
                $productCategory->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
            }
            if( $request->media_objects['headerImage'] ){
                $productCategory->library_media()->attach($request->media_objects['headerImage']['id'],['collection' => 'headerImage']);
            }
            if( $request->media_objects['mainVideo'] ){
                $productCategory->library_media()->attach($request->media_objects['mainVideo']['id'],['collection' => 'mainVideo']);
            }
            if( $request->media_objects['galleryImages'] ){
                $productCategory->library_media()->attach(Arr::pluck($request->media_objects['galleryImages'],'id'),['collection' => 'galleryImages']);
            }
        }

        //breadcrumb
        $bc = saveBreadCrumb($productCategory);

        return redirect()->route('product-categories.index',['productType' => $productCategory->product_type_id, 'language' => $productCategory->language])->with('flash',[
            'type' => 'success',
            'message' => 'Ürün kategorisi güncellendi.',
            'data' => $productCategory
        ]);
    }


    public function delete(Request $request, ProductCategory $productCategory)
    {
        if ($request->user()->cannot('delete-'.$this->productType->uuid)) {
            abort(403);
        }

        $current_parent_id = $productCategory->parent_id;

        $parent_degistir = ProductCategory::where('parent_id',$productCategory->id)->update([
            "parent_id" => $current_parent_id ?? null
        ]);

        $productCategory->delete();

        return back();
    }


    public function restore(Request $request, ProductCategory $productCategory)
    {
        if ($request->user()->cannot('delete-'.$this->productType->uuid)) {
            abort(403);
        }

        $productCategory->restore();
        return back();
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ProductCategory $productCategory)
    {
        if ($request->user()->cannot('delete-'.$this->productType->uuid)) {
            abort(403);
        }
    }



    //BULK
    public function bulkDelete(Request $request)
    {
        if ($request->user()->cannot('delete-'.$this->productType->uuid)) {
            abort(403);
        }

        $categories = ProductCategory::whereIn('id',$request->items)->delete();
        return back()->with('flash',[
            'type' =>'success',
            'message' => "Seçilen kategoriler silindi!"
        ]);
    }


}
