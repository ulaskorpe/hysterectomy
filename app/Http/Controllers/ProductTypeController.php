<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Form;
use App\Models\Product;
use App\Models\Taxonomy;
use App\Models\ProductType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\PermissionGroup;
use Illuminate\Validation\Rule;
use App\Models\SpatiePermission;
use App\Models\ProductOptionGroup;
use App\Models\Scopes\LanguageScope;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Exports\ExportProductTypeSales;
use Illuminate\Database\Eloquent\Builder;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return ProductType::with('categories')->get();
        };

        $this->authorize('viewAny', ProductType::class);

        $types = QueryBuilder::for(ProductType::class)
            ->with(['taxonomy','connected_product_types'])
            ->get();

        return Inertia('ProductType/Index', [
            'product_types' => $types,
            'filters' => [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', ProductType::class);

        $copy_content = null;

        if ($request->has('uuid') && $request->has('from_id')) {

            //once kayit varmi bakalim.
            $exists = ProductType::withTrashed()->where('uuid', $request->uuid)->first(); //scope var language direkt aliyor.
            if ($exists) {
                return back()->with('flash', [
                    'type' => 'error',
                    'message' => 'Seçilen dil için bağlantılı bir içerik bulunuyor!'
                ]);
            }

            $copy_content_model = ProductType::withoutGlobalScope(LanguageScope::class)
                ->where('id', $request->from_id)->first();

            if ($copy_content_model) {
                $copy_content = $copy_content_model;
            }
        }

        return Inertia('ProductType/Create', [
            'optionGroups' => ProductOptionGroup::all(),
            'forms' => Form::all()->map(function ($form) {
                return [
                    'id' => $form->id,
                    'name' => $form->name,
                ];
            }),
            'taxes' => Tax::all()->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                ];
            }),
            'copy_content' => $copy_content
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ProductType::class);

        $request->validate([
            'name' => 'required|string|max:50',
            'after_order_type' => 'required',
        ]);

        $type = ProductType::create($request->all());

        session()->forget('productTypes');
        
        if (empty($request->uuid)) {
            $permission_group = PermissionGroup::create([
                'name' => $type->name . ' Yönetimi',
                'model' => 'Product'
            ]);

            $permissionEk = $type->uuid;

            $view = SpatiePermission::create([
                'name' => 'view-' . $permissionEk,
                'view_name' => 'Görüntüle',
                'permission_group_id' => $permission_group->id
            ]);

            $create = SpatiePermission::create([
                'name' => 'create-' . $permissionEk,
                'view_name' => 'Yeni Ekle',
                'permission_group_id' => $permission_group->id
            ]);

            $edit = SpatiePermission::create([
                'name' => 'edit-' . $permissionEk,
                'view_name' => 'Düzenle',
                'permission_group_id' => $permission_group->id
            ]);

            $delete = SpatiePermission::create([
                'name' => 'delete-' . $permissionEk,
                'view_name' => 'Sil',
                'permission_group_id' => $permission_group->id
            ]);
        }

        return redirect()->route('product-types.index', ['language' => $type->language])->with('flash', [
            'type' => 'success',
            'message' => 'Ürün tipi oluşturuldu.',
            'data' => $type
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductType $productType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductType $productType)
    {
        $this->authorize('update', ProductType::class);

        return Inertia('ProductType/Edit', [
            'productType' => $productType->load(['taxonomy']),
            'optionGroups' => ProductOptionGroup::all(),
            'forms' => Form::all()->map(function ($form) {
                return [
                    'id' => $form->id,
                    'name' => $form->name,
                ];
            }),
            'taxes' => Tax::all()->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                ];
            }),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductType $productType)
    {
        $this->authorize('update', ProductType::class);

        if ($request->slug) {
            $request->merge([
                'slug' => Str::slug($request->slug)
            ]);
        }

        if ($request->taxonomy) {
            $request->merge([
                'taxonomy' => Str::slug($request->taxonomy)
            ]);
        }


        $request->validate([
            'name' => 'required|string|max:50',
            'after_order_type' => 'required',
            'taxonomy' => [
                'required',
                'string',
                Rule::unique('taxonomies', 'name')->ignore($productType->id,'model_id')
            ],
            'slug' => ['required','string','unique:product_types,slug,'.$productType->id],
        ]);

        $productType->fill($request->all());
        $productType->save();

        if ($request->taxonomy != $productType->taxonomy->name) {

            $new_taxonomy = $request->taxonomy;
            $counter = 1;

            while (!Taxonomy::isUnique($new_taxonomy)) {
                $new_taxonomy = $request->taxonomy . '-' . $counter;
                $counter++;
            }

            $productType->taxonomy()->update([
                'name' => $new_taxonomy
            ]);
        }

        session()->forget('productTypes');

        return back()->with('flash', [
            'type' => 'success',
            'message' => 'Ürün tipi güncellendi.',
            'data' => $productType
        ]);
    }


    public function delete(ProductType $productType)
    {
        $this->authorize('delete', ProductType::class);

        $productType->delete();
        return back();
    }


    public function restore(ProductType $productType)
    {
        $this->authorize('restore', ProductType::class);

        $productType->restore();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $productType)
    {
        //
    }




    //sales
    public function sales(Request $request, ProductType $productType)
    {

        if ($request->user()->cannot('view-' . $productType->uuid)) {
            abort(403);
        }

        $productType->setAppends([])->loadMissing([
            'option_group:id,name',
            'option_group.options:id,name,product_option_group_id'
        ]);

        if ($productType->option_group) {
            $productType->option_group->options->each->setAppends([]);
        }

        $sales_query = QueryBuilder::for(OrderProduct::class)
            ->whereHas('order')
            ->with(['order' => function ($o) {
                $o->with(['campaign'])->with(['coupon' => function ($c) {
                    $c->select('id', 'code')->withTrashed();
                }]);
            }])
            ->with(['product' => function ($o) {
                $o->withoutGlobalScopes()
                    ->whereHas('product_type', function ($ptype) {
                        $ptype->where('is_cartable', true);
                    });
            }])
            ->with(['product_variant' => function ($o) {
                $o->withoutGlobalScopes()
                    ->withTrashed();
            }])
            ->whereHas('product', function ($prod) use ($productType) {
                $prod->where('product_type_id', $productType->id);
            })
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('order', function (Builder $q) use ($search) {
                    $q->where('cart_details', 'like', '%' . $search . '%');
                });
            })
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::callback('has_coupon', function (Builder $query, $value) {
                    if ($value == "1") {
                        $query->whereHas('order', function ($cou) {
                            $cou->whereNotNull('coupon_id');
                        });
                    }
                    if ($value == "0") {
                        $query->whereHas('order', function ($cou) {
                            $cou->whereNull('coupon_id');
                        });
                    }
                }),
                AllowedFilter::scope('start_date'),
                AllowedFilter::scope('end_date'),
                AllowedFilter::callback('products', function (Builder $query, $value) {
                    $value = array_map('intval', $value);
                    $query->whereIn('product_id', $value);
                })
            ]);


        if ($request->has('export') && $request->export === "true") {

            $sales = $sales_query->get();

            $export = new ExportProductTypeSales($sales->toArray(),$productType);

            return Excel::download($export, $productType->name.' - Satış Detayı.xlsx');
        }

        $sales = $sales_query->paginate(30)->withQueryString();

        return Inertia('ProductType/Sales', [
            'sales' => $sales,
            'product_type' => $productType,
            'filters' => [
                'start_date' => [
                    'label' => 'Satış Başlangıç Tarihi',
                    'type' => 'date',
                    'options' => [],
                    'value' => isset($request->filter['start_date']) ? Carbon::parse($request->filter['start_date'])->setTimezone('Europe/Istanbul') : "",
                ],
                'end_date' => [
                    'label' => 'Satış Bitiş Tarihi',
                    'type' => 'date',
                    'options' => [],
                    'value' => isset($request->filter['end_date']) ? Carbon::parse($request->filter['end_date'])->setTimezone('Europe/Istanbul') : "",
                ],
                'products' => [
                    'label' => 'Ürün',
                    'type' => 'select',
                    'options' => Product::select('id', 'name')->where('product_type_id', $productType->id)->get()->map(function ($st) {
                        return [
                            'label' => $st->name,
                            'value' => (string)$st->id // query params string olarak aliyor. vue multiselect de eslesmesi icin string
                        ];
                    }),
                    'value' => $request->filter['products'] ?? [],
                    'full_width' => true
                ],
            ],
        ]);
    }
}
