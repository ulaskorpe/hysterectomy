@php
    
    $blade_filters = [];
    
    if( $filters['product_types']['active'] ) {

        $blade_filters['product_types'] = [
            'label' => $filters['product_types']['label'],
            'type' => 'check',
            'options' => \App\Models\ProductType::select('id','name')->get()->map(function($ptype){
                return [
                    'label' => $ptype->name,
                    'value'=> $ptype->id
                ];
            }),
            'value' => request()->filter['product_types'] ?? [],
        ];

    }


    if( $filters['categories']['active'] ) { 

        $blade_filters['categories'] = [
            'label' => $filters['categories']['label'],
            'type' => 'check',
            'options' => \App\Models\ProductCategory::select('id','name','product_type_id')->when($productType,function($qq) use ($productType) {
                $qq->where('product_type_id',$productType->id);
            })
            ->get()->map(function($category){
                return [
                    'label' => $category->name,
                    'value'=> $category->id
                ];
            }),
            'value' => request()->filter['categories'] ?? [],
        ];

    }


    if( $filters['price']['active'] ) {

        $min_max = \App\Models\ProductPrice::selectRaw('MIN(final_price) as min_price, MAX(final_price) as max_price')
        ->when($productType,function($query) use ($productType) {
            $query->whereHas('priceable',function($model) use ($productType) {

                $classBaseName = class_basename($model->getModel());

                if( $classBaseName == 'Product' ){
                    $model->where('product_type_id',$productType->id);
                }
                if( $classBaseName == 'ProductVariant' ){
                    $model->whereHas('product',function($product) use ($productType) {
                        $product->where('product_type_id',$productType->id);
                    });
                }
            });
        })->first();


        $blade_filters['price'] = [
            'label' => $filters['price']['label'],
            'type' => 'range',
            'options' => [$min_max->min_price,$min_max->max_price],
            'value' => request()->filter['price'] ?? [],
        ];

    }


    if( $filters['attributes']['active'] ) {

        $product_attributes = \App\Models\ProductAttribute::with('values')
        ->where('fe_visible',true)
        ->when($productType,function($query) use ($productType) {
            $query->whereHas('product_types',function($ptype) use ($productType) {
                $ptype->where('id',$productType->id);
            });
        })
        ->get();

        foreach ($product_attributes as $a => $attribute) {
            if( $attribute->option_type == 'select' || $attribute->option_type == 'multiselect'){

                $inputName = Str::slug($attribute->name,'_');

                $blade_filters[$inputName] = [
                    'label' => $attribute->name,
                    'type' => 'check',
                    'options' => $attribute->values->map(function($val){
                        return [
                            'label' => $val['name'],
                            'value'=> $val['id']
                        ];
                    }),
                    'value' => request()->filter[$inputName] ?? [],
                ];

            }
        }

    }

@endphp



<form class="sticky-filters d-flex flex-column justify-content-between h-100" method="GET" action="{{ request()->url() }}#products-list" onsubmit="this.querySelector('[type=submit]').disabled=true;">

    <div>
        @if (request()->has('filter') && $useActiveFilters)
        <div class="d-flex flex-column">
            @foreach ($blade_filters as $s => $filter)
            @if (count($filter['value']) > 0)
            <div class="p-3 border rounded">
                <span class="fs-xs fw-bold ">{{ $filter['label'] }}</span>
                <div class="hstack gap-1 flex-wrap">
                    @foreach ($filter['value'] as $a => $selected)
                    @php
                        $op = [
                            'label' => "",
                        ];
                        if( $filter['type'] == 'check' ){
                            $op = Arr::first($filter['options'], function ($value, $key) use($selected) {
                                return $value['value'] == $selected;
                            });
                        }
                        if( $s == 'price' ){
                            $op = [
                                'label' => formatNumberToCurrency($selected)
                            ];
                        }
                    @endphp
                    <button 
                        type="button" 
                        class="btn bg-secondary text-white px-2 py-1 fs-xs" 
                        onclick="this.closest('form').querySelector('#{{$s.'-'.$selected}}').click()"
                    >
                        <span>{{$op['label']}}<i class="bi bi-x-lg ms-2"></i></span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        @foreach ($blade_filters as $key => $filter)
        @if (count($filter['options']) > 0)
        <div class="d-flex flex-column rounded border mb-3">
            <div class="text-uppercase px-3 py-2 bg-light mb-0 fw-semibold fs-sm border-bottom border-top">{{ $filter['label'] }}</div>
            <div class="p-3">
                <div class="overflow-hidden">

                    @if ($filter['type'] == 'check')
                    <div class="d-flex flex-column max-h-150px" data-simplebar data-simplebar-auto-hide="false">
                        <div>
                            @foreach ($filter['options'] as $o => $option)
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    value="{{$option['value']}}" 
                                    name="filter[{{$key}}][]" 
                                    id="{{$key.'-'.$option['value']}}" 
                                    onchange="this.closest('form').querySelector('[type=submit]').disabled=false;" 
                                    @if (isset(request()->input('filter')[$key]) && in_array($option['value'],request()->input('filter')[$key])) checked @endif
                                />
                                <label class="form-check-label" for="{{$key.'-'.$option['value']}}"><small>{{ $option['label'] }}</small></label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if ($filter['type'] == 'range')
                    <div class="d-flex flex-column">
                        <div>
                            <label class="form-check-label d-flex flex-row align-items-center justify-content-between" for="{{$key.'-min'}}">
                                <small>Min</small>
                                <small id="{{$key.'-min-value'}}" class="fw-bold">{{formatNumberToCurrency(isset(request()->input('filter')[$key]) ? request()->input('filter')[$key][0] : $filter['options'][0])}}</small>
                            </label>
                            <input 
                                class="form-range" 
                                type="range" 
                                step="1" 
                                min="{{$filter['options'][0]}}" max="{{$filter['options'][1]}}" 
                                value="{{isset(request()->input('filter')[$key]) ? request()->input('filter')[$key][0] : $filter['options'][0]}}" 
                                name="filter[{{$key}}][]" 
                                id="{{$key.'-min'}}" 
                                oninput="document.getElementById('{{$key.'-min-value'}}').innerText = formatCurrency(this.value) + ' TL'" 
                                onchange="this.closest('form').querySelector('[type=submit]').disabled=false;" 
                            />
                        </div>
                        <div>
                            <label class="form-check-label d-flex flex-row align-items-center justify-content-between" for="{{$key.'-max'}}">
                                <small>Max</small>
                                <small id="{{$key.'-max-value'}}" class="fw-bold">{{formatNumberToCurrency(isset(request()->input('filter')[$key]) ? request()->input('filter')[$key][1] : $filter['options'][1])}}</small>
                            </label>
                            <input 
                                class="form-range" 
                                type="range" 
                                step="1" 
                                min="{{$filter['options'][0]}}" max="{{$filter['options'][1]}}" 
                                value="{{isset(request()->input('filter')[$key]) ? request()->input('filter')[$key][1] : $filter['options'][1]}}" 
                                name="filter[{{$key}}][]" 
                                id="{{$key.'-max'}}" 
                                oninput="document.getElementById('{{$key.'-max-value'}}').innerText = formatCurrency(this.value) + ' TL'" 
                                onchange="this.closest('form').querySelector('[type=submit]').disabled=false;" 
                            />
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        @endif
        @endforeach

        <input type="hidden" name="sort" value="{{request()->input('sort')}}">
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary w-100" disabled>{{ __('UYGULA') }}</button>
    </div>

</form>