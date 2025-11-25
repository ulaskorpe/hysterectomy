<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Link;
use App\Models\Product;
use App\Models\Appointment;
use App\Models\WorkingHour;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\ContentCategory;
use App\Models\Scopes\LanguageScope;
use Illuminate\Support\Facades\Cache;

class FetchController extends Controller
{
    
    public function contentCategories(Request $request) {

        if( $request->wantsJson() ){

            return ContentCategory::select('id','name','uuid','language')->get();

        }

    }

    public function links(Request $request) {

        $links = Link::with(['linkable' => function($qu){
            $qu->withoutGlobalScope(LanguageScope::class);
        }])
        ->whereHas('linkable')
        ->whereNotIn('linkable_type',['App\\Models\\Tag'])
        ->get()
        ->filter(function ($link) {
            return $link->linkable->is_published;
        })
        ->map(function($link){

            $title = $link->linkable->name;
            $model = class_basename($link->linkable);

            return [
                'title' => $title,
                'value' => $link->final_uri
            ];
        });

        if( $request->wantsJson() ){

            return $links;

        }

    }



    public function allProducts(Request $request) {

        if( $request->wantsJson() ){

            return Product::select('id','name','uuid','language')->withoutGlobalScope(LanguageScope::class)->get();

        }

    }


    public function productDetails(Request $request,Product $product){

        $cacheKey = 'fetch_product_details_'.$product->id;

        $producttt = Cache::remember($cacheKey, now()->addHour(), function () use ($product) {
            $product->loadMissing([
                'product_type',
                'product_type.option_group',
                'product_type.option_group.options',
                'product_type.product_customer_attributes',
                'library_media',
                'product_variants',
                'product_variants.library_media',
                'product_price',
                'product_categories:id,name,language,uuid',
                'product_categories.uri',
                'tags',
                'uri'
            ]);

            return $product;
        });


        $blade = $producttt->product_type->is_cartable ? 'components.product-layout.product-details-cartable' : 'components.product-layout.product-details-noncartable';

        if( $product->layout || $product->product_type->product_layout ){
            $blade = 'components.product-layout.product-details-dynamic';
        }

        $html = view($blade,[
            'product' => $producttt,
            'popup' => true
        ])->render();

        return response()->json([
            'html' => $html
        ]);

    }

    public function variantSelectionOld(Request $request,Product $product){

        $producttt = $product->loadMissing([
            'product_categories',
            'product_price',
            'library_media',
            'uri',
            'product_type',
            'product_variants',
        ]);

        $html = view('components.product-variants-step-by-step',[
            'product' => $producttt
        ])->render();

        return response()->json([
            'html' => $html
        ]);

    }

    public function variantSelection(Request $request,Product $product){

        $producttt = $product->loadMissing([
            'product_categories:id,name',
            'product_price',
            'library_media',
            'uri',
            'product_type',
            'product_type.option_group',
            'product_type.option_group.options',
            'product_variants',
            'product_variants.price',
        ]);

        $html = view('components.product-variants-step-by-step',[
            'product' => $producttt
        ])->render();

        return response()->json([
            'html' => $html
        ]);

    }

    public function variantPrepareAddToCartButton(Request $request,ProductVariant $productVariant){

        $productVariantttt = $productVariant->load([
            'option_group',
            'price',
        ]);

        $html = view('components.product-layout.product-layout-basket-buttons-variant',[
            'productVariant' => $productVariantttt
        ])->render();

        return response()->json([
            'html' => $html,
        ]);

    }


    public function productOptionSelection(Request $request,Product $product){

        $html = view('components.product-variants-default',[
            'product' => $product
        ])->render();

        return response()->json([
            'html' => $html
        ]);

    }


    public function getAvailableWorkingHourSlots(Request $request){

        $date = $request->query('date');
        $cleanDate = preg_replace('/\s*\(.*\)$/', '', $date);
        $cleanDate = str_replace('GMT ', 'GMT+', $cleanDate);
        $carbonDate = Carbon::createFromFormat('D M d Y H:i:s \G\M\TO', $cleanDate);
        $dayOfWeek = $carbonDate->dayOfWeekIso;
        $workingHour = WorkingHour::where('day_of_week', $dayOfWeek)->where('is_active',true)->first();

        if (!$workingHour) {
            return response()->json(['slots' => []]);
        }

        $start = \Carbon\Carbon::parse($workingHour->start_time);
        $end   = \Carbon\Carbon::parse($workingHour->end_time);

        $slots = [];
        while ($start < $end) {
            $slotTime = $start->format('H:i');
            
            // 12:30'u atla
            if ($slotTime === '12:30') {
                $start->addMinutes(30);
                continue;
            }

            $dbDate = $carbonDate->format('Y-m-d');
            $exists = Appointment::whereDate('appointment_date', $dbDate)
                ->where('appointment_time', $slotTime.':00')
                ->whereIn('status',['new','success'])
                ->exists();

            $slots[] = [
                'time' => $slotTime,
                'available' => !$exists
            ];

            $start->addMinutes(30); // slot süresi
        }

        return response()->json(['slots' => $slots]);

    }


}
