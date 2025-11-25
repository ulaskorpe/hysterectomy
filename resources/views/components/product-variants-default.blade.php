@php
    
    $selectedParams = [];
    $queryParams = [];
    $hasMultipleSelectOptionCount = 0;
    $option_selected_count = 0;


    $minMaxPrices = $product->product_variants()
    ->join('product_prices', 'product_variants.id', '=', 'product_prices.priceable_id')
    ->where('product_prices.priceable_type','App\Models\ProductVariant')
    ->whereNull('product_variants.deleted_at')
    ->selectRaw('MIN(product_prices.price) as min_price,MIN(product_prices.final_price) as min_final_price, MAX(product_prices.price) as max_price, MAX(product_prices.final_price) as max_final_price')
    ->first();

    $minPrice = formatNumberToCurrency($minMaxPrices->min_price);
    $maxPrice = formatNumberToCurrency($minMaxPrices->max_price);
    $minFinalPrice = formatNumberToCurrency($minMaxPrices->min_final_price);
    $maxFinalPrice = formatNumberToCurrency($minMaxPrices->max_final_price);


    $product_type_option_group = $product->product_type->option_group;
    $product_type_options = $product_type_option_group->options->loadMissing('option_values');
    
    $variants_query = $product->product_variants()->with('main_image')->with('price');
    
    // her bir option icin request param geldiyse variantlar icinde ariyoruz.
    foreach ($product_type_options as $key => $option) {
        
        $param = request()->input($option->slug) && request()->input($option->slug) != "" ? request()->input($option->slug) : null;

        if($param){

            $queryParams[$option->slug] = $param;

            $variants_query->whereJsonContains('option_values',[['slug' => $option->slug]])->whereJsonContains('option_values',[['value' => $queryParams[$option->slug]]]);

            $selectedParams[$option->id] = $param;

            $option_selected_count++;
            
            if($option->option_type == 'multiselect'){
                $hasMultipleSelectOptionCount++;
            }
        }

    }

    $variants = $variants_query->get();

    //dd($variants);

@endphp

<div id="product-variant-selections" class="d-flex flex-column gap-3 mb-4">

    @foreach ($product_type_options as $o => $option)
    <div @class(['d-flex flex-column gap-3 pt-3 border-top'])>
        <div @class(['row','align-items-center','g-0'])>

            <div class="col-md-4 col-xl-3 col-xxl-2 mb-2 mb-md-0">
                <span class="fw-semibold">{{$option->name}}</span>
            </div>
            
            <div class="col-md-8 col-xl-9 col-xxl-10">
            {{-- View type a gore gorunum --}}
            @switch($option->site_view)

                @case('radio_button')
                
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    @foreach ($option->option_values as $v => $val)
        
                        @php 
        
                            $variantsHasVal = $variants->first(function ($variant) use ($option, $val) {
                                return collect($variant['option_values'])->where('slug', $option->slug)->where('value', $val->name)->isNotEmpty();
                            });
        
                            $isDisabled = $variantsHasVal ? false : true;
        
                            $isChecked = request()->input($option->slug) && request()->input($option->slug) == $val->name;
        
                        @endphp
        
                        <div data-bs-toggle="tooltip" title="{{ $val->name }}">
                            
                            <input 
                                onchange='productOptionSelection(this,@json($queryParams))' 
                                data-slug="{{$option->slug}}" 
                                data-product-uuid="{{$product->uuid}}" 
                                required type="radio" 
                                @class(['btn-check']) 
                                name="{{$option->slug}}" 
                                value="{{$val->name}}" 
                                id="{{$option->slug}}-{{$val->id}}" 
                                autocomplete="off" 
                                @checked($variants->count() == 1 && !$isDisabled ? true : $isChecked) 
                            >
        
                            {{-- buton label alanlarini renk ise renk kutusu, gorsel ise gorsel yapalim. --}}
                            <label 
                                @class([
                                    'btn btn-sm btn-outline-dark px-2 py-1',
                                    'border-primary bg-primary' => $variants->count() == 1 && !$isDisabled ? true : $isChecked,
                                ]) 
                                for="{{$option->slug}}-{{$val->id}}"
                            >{{$val->name}}</label>
        
                        </div>
        
                    @endforeach
                </div>

                @break
                
                @case('color')
                
                <div class="d-flex flex-wrap gap-1 align-items-center mb-n1">
                    @foreach ($option->option_values as $v => $val)
        
                        @php 
        
                            $variantsHasVal = $variants->first(function ($variant) use ($option, $val) {
                                return collect($variant['option_values'])->where('slug', $option->slug)->where('value', $val->name)->isNotEmpty();
                            });
        
                            $isDisabled = $variantsHasVal ? false : true;
        
                            $isChecked = request()->input($option->slug) && request()->input($option->slug) == $val->name;
        
                        @endphp
        
                        <div data-bs-toggle="tooltip" title="{{ $val->name }}">
                            
                            <input 
                                onchange='productOptionSelection(this,@json($queryParams))' 
                                data-slug="{{$option->slug}}" 
                                data-product-uuid="{{$product->uuid}}" 
                                required type="radio" 
                                @class(['btn-check']) 
                                name="{{$option->slug}}" 
                                value="{{$val->name}}" 
                                id="{{$option->slug}}-{{$val->id}}" 
                                autocomplete="off" 
                                @checked($variants->count() == 1 && !$isDisabled ? true : $isChecked) 
                            >
        
                            {{-- buton label alanlarini renk ise renk kutusu, gorsel ise gorsel yapalim. --}}
                            <label 
                                @class([
                                    'overflow-hidden rounded-circle',
                                    'cursor-pointer border border-2',
                                    'border-dark' => $variants->count() == 1 && !$isDisabled ? true : $isChecked,
                                ]) 
                                for="{{$option->slug}}-{{$val->id}}"
                            >
                                <span class="d-flex w-25px h-25px m-1 rounded-circle" style="background-color:#{{$val->color_value}}"></span>
                            </label>
        
                        </div>
        
                    @endforeach
                </div>

                @break

                @case('image')
                
                <div class="d-flex flex-wrap gap-1 align-items-center mb-n1">
                    @foreach ($option->option_values as $v => $val)
        
                        @php 
        
                            $variantsHasVal = $variants->first(function ($variant) use ($option, $val) {
                                return collect($variant['option_values'])->where('slug', $option->slug)->where('value', $val->name)->isNotEmpty();
                            });
        
                            $isDisabled = $variantsHasVal ? false : true;
        
                            $isChecked = request()->input($option->slug) && request()->input($option->slug) == $val->name;
        
                        @endphp
        
                        <div data-bs-toggle="tooltip" title="{{ $val->name }}">
                            
                            <input 
                                onchange='productOptionSelection(this,@json($queryParams))' 
                                data-slug="{{$option->slug}}" 
                                data-product-uuid="{{$product->uuid}}" 
                                required type="radio" 
                                @class(['btn-check']) 
                                name="{{$option->slug}}" 
                                value="{{$val->name}}" 
                                id="{{$option->slug}}-{{$val->id}}" 
                                autocomplete="off" 
                                @checked($variants->count() == 1 && !$isDisabled ? true : $isChecked) 
                            >
        
                            {{-- buton label alanlarini renk ise renk kutusu, gorsel ise gorsel yapalim. --}}
                            <label 
                                @class([
                                    'overflow-hidden',
                                    'cursor-pointer border border-3 rounded-3 p-1',
                                    'border-primary' => $variants->count() == 1 && !$isDisabled ? true : $isChecked,
                                ]) 
                                for="{{$option->slug}}-{{$val->id}}"
                            >
                                <img src="{{$val->image_uri}}" width="50" height="50" class="rounded object-fit-cover" />
                            </label>
        
                        </div>
        
                    @endforeach
                </div>

                @break
                
                @default
                
                <select 
                    name="{{$option->slug}}" 
                    class="form-control form-control-sm" 
                    onchange='productOptionSelection(this,@json($queryParams))' 
                    data-slug="{{$option->slug}}" 
                    data-product-uuid="{{$product->uuid}}"
                >
                    <option value="">Seçiniz</option>
                    @foreach ($option->option_values as $v => $val)

                        @php 

                            $variantsHasVal = $variants->first(function ($variant) use ($option, $val) {
                                return collect($variant['option_values'])->where('slug', $option->slug)->where('value', $val->name)->isNotEmpty();
                            });

                            $isDisabled = $variantsHasVal ? false : true;

                            $isChecked = request()->input($option->slug) && request()->input($option->slug) == $val->name;

                        @endphp

                        <option value="{{$val->name}}" @selected($variants->count() == 1 && !$isDisabled ? true : $isChecked)>{{$val->name}}</option>

                    @endforeach
                </select>

            @endswitch
            </div>


        </div>
    </div>
    @endforeach

    @if (count($selectedParams) > 0 && $variants->count() == 1)
    <div class="d-flex align-items-center mb-0 gap-3 mt-3 pt-3 border-top">

        <div class="d-flex flex-column fs-xs">
            @foreach ($variants[0]->option_values as $o => $option)
            <span>{{ $option['name'] }}: <span class="fw-semibold">{{ $option['value'] }}</span></span>
            @endforeach
        </div>

        @if ($product_type_option_group->has_own_price && $minFinalPrice != $maxFinalPrice)
        <div class="ps-3 border-start">
            @if ($variants[0]->price->discount)
            <span class="text-muted fs-xs"><del>{{formatNumberToCurrency($variants[0]->price->price)}}</del></span>
            @endif
            <span id="variant-price" class="lead-responsive mb-0 fw-bold">{{formatNumberToCurrency($variants[0]->price->final_price)}}</span>
        </div>
        @endif
        
    </div>
    @endif

    @if ($variants->count() == 0)
    <div class="alert alert-danger d-flex align-items-center mt-3 mb-0 fs-xs" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill me-2" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
        </svg>
        <div>Seçilen kriterlere uygun ürün bulunmamaktadır.</div>
    </div>
    @endif

    <input type="hidden" name="is_variant" value="{{true}}"/>
    <input type="hidden" name="variant_id" value="{{ $variants->count() == 1 ? $variants[0]->id : '' }}"/>

    @if ($variants->count() > 0)
    <div id="product-variant-basket-buttons" class="mt-4">
        <x-product-layout.product-layout-basket-buttons-variant :product="$product" :product-variant="$variants->count() == 1 ? $variants[0] : null"/>
    </div>
    @endif

</div>