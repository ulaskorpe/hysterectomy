<?php

namespace App\Http\Controllers;

use App\Models\SeoData;
use App\Models\Campaign;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Rules\CheckDateFormat;
use Illuminate\Support\Carbon;
use App\Models\Scopes\LanguageScope;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $this->authorize('viewAny', Campaign::class);

        $campaigns = QueryBuilder::for(Campaign::class)
        ->with(['product_types:id,name','uri','connected_campaigns'])
        ->when($request->input('search'), function ($query, $search) {
            $query->where('code','like','%'.$search.'%');
        })
        ->defaultSort('-created_at')
        ->allowedSorts('created_at')
        ->allowedFilters([
            AllowedFilter::callback('users_only', function (Builder $query, $value) {
                if( $value === "1" ){
                    $query->where('users_only', 1);
                }
                if( $value === "0" ){
                    $query->where('users_only', 0);
                }
            }),
        ])
        ->paginate()
        ->withQueryString();
        
        return Inertia('Campaign/Index',[
            'campaigns' => $campaigns,
            'filters' => [
                'users_only' => [
                    'label' => 'Üyelere Özel',
                    'type' => 'selectSingle',
                    'options' => [
                        [
                            'label' => 'Evet',
                            'value'=> "1"
                        ],
                        [
                            'label' => 'Hayır',
                            'value'=> "0"
                        ]
                    ],
                    'value' => $request->filter['users_only'] ?? [],
                ],
            ],
        ]);
    }



    public function trash(Request $request)
    {

        $this->authorize('delete', Campaign::class);

        $campaigns = [];
        $search = $request->search;

        $campaigns = QueryBuilder::for(Campaign::class)
        ->onlyTrashed()
        ->when($search && !empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name','like','%'.$search.'%')->orWhere('summary','like','%'.$search.'%');
            });
        })->paginate(30)->withQueryString()->toArray();

        return Inertia('Campaign/Trash',[
            'campaigns' => $campaigns,
            'filters' => [],
        ]);

        
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Campaign::class);

        $copy_content = null;

        if( $request->has('uuid') && $request->has('from_id') ){
            
            //once kayit varmi bakalim.
            $exists = Campaign::withTrashed()->where('uuid',$request->uuid)->first(); //scope var language direkt aliyor.
            if( $exists ){
                return back()->with('flash',[
                    'type' => 'error',
                    'message' => 'Seçilen dil için bağlantılı bir kampanya bulunuyor!'
                ]);
            }

            $copy_content_model = Campaign::withoutGlobalScope(LanguageScope::class)
            ->where('id',$request->from_id)->first();

            if($copy_content_model){
                $copy_content_model->media_objects = [
                    'mainImage' => $copy_content_model->library_media_group['mainImage'][0] ?? null,
                    'headerImage' => $copy_content_model->library_media_group['headerImage'][0] ?? null,
                    'mainVideo' => $copy_content_model->library_media_group['mainVideo'][0] ?? null,
                    'headerVideo' => $copy_content_model->library_media_group['headerVideo'][0] ?? null,
                    'galleryImages' => $copy_content_model->library_media_group['galleryImages'] ?? null,
                    'mainFile' => $copy_content_model->library_media_group['mainFile'][0] ?? null,
                ];

                $copy_content = $copy_content_model;
            }

        }
        
        return Inertia('Campaign/Create',[
            'copy_content' => $copy_content
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Campaign::class);

        $validation_data = [
            'name' => 'required|string|max:191',
            'seo.title' => 'required|string',
            'users_only' => 'required',
            'type' => 'required',
            'discount' => 'required',
            'start_date' => ['required',new CheckDateFormat],
            'end_date' => ['required',new CheckDateFormat],
            'apply_cart' => 'required',
            'min_cart_amount' => 'required',
            'user_usage_count' => 'required',
        ];

        $request->validate($validation_data);

        $campaign = Campaign::create($request->all());

        if( $request->product_types && !$request->apply_cart ){
            $campaign->product_types()->attach($request->product_types);
        }

        if( $request->tags ){
            $campaign->tags()->attach($request->tags);
        }

        if ($request->media_objects) {
            if( $request->media_objects['mainImage'] ){
                $campaign->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
            }
            if( $request->media_objects['headerImage'] ){
                $campaign->library_media()->attach($request->media_objects['headerImage']['id'],['collection' => 'headerImage']);
            }
            if( $request->media_objects['mainVideo'] ){
                $campaign->library_media()->attach($request->media_objects['mainVideo']['id'],['collection' => 'mainVideo']);
            }
            if( $request->media_objects['galleryImages'] ){
                $campaign->library_media()->attach(Arr::pluck($request->media_objects['galleryImages'],'id'),['collection' => 'galleryImages']);
            }
        }

        $request->merge([
            'start_date' => $request->start_date ? Carbon::parse($request->start_date)->setTimezone('Europe/Istanbul') : now(),
            'end_date' => $request->end_date ? Carbon::parse($request->start_date)->setTimezone('Europe/Istanbul') : null,
            'pivot_data' => [
                'media_objects' => $request->media_objects,
            ]
        ]);
        
        //breadcrumb
        $bc = saveBreadCrumb($campaign);

        return redirect()->route('campaigns.index',['language' => $campaign->language])->with('flash',[
            'type' => 'success',
            'message' => 'Kampanya oluşturuldu.',
            'data' => $campaign
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        $this->authorize('view', Campaign::class);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        $this->authorize('update', Campaign::class);

        $campaign->media_objects = [
            'mainImage' => $campaign->library_media_group['mainImage'][0] ?? null,
            'headerImage' => $campaign->library_media_group['headerImage'][0] ?? null,
            'mainVideo' => $campaign->library_media_group['mainVideo'][0] ?? null,
            'headerVideo' => $campaign->library_media_group['headerVideo'][0] ?? null,
            'galleryImages' => $campaign->library_media_group['galleryImages'] ?? null,
            'mainFile' => $campaign->library_media_group['mainFile'][0] ?? null,
        ];

        return Inertia('Campaign/Edit',[
            'campaign' => $campaign->loadMissing(['tags:id,name','product_types:id,name'])->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $this->authorize('update', Campaign::class);

        $validation_data = [
            'name' => 'required|string|max:191',
            'seo.title' => 'required|string',
            'users_only' => 'required',
            'type' => 'required',
            'discount' => 'required',
            'start_date' => ['required',new CheckDateFormat],
            'end_date' => ['required',new CheckDateFormat],
            'apply_cart' => 'required',
            'min_cart_amount' => 'required',
            'user_usage_count' => 'required',
        ];

        $request->validate($validation_data);

        $campaign->fill($request->all());

        $campaign->save();

        $campaign->tags()->detach();
        $campaign->library_media()->detach();
        $campaign->product_types()->detach();

        if( $request->product_types && !$request->apply_cart){
            $campaign->product_types()->attach($request->product_types);
        }

        if( $request->tags ){
            $campaign->tags()->attach($request->tags);
        }

        if ($request->media_objects) {
            if( $request->media_objects['mainImage'] ){
                $campaign->library_media()->attach($request->media_objects['mainImage']['id'],['collection' => 'mainImage']);
            }
            if( $request->media_objects['headerImage'] ){
                $campaign->library_media()->attach($request->media_objects['headerImage']['id'],['collection' => 'headerImage']);
            }
            if( $request->media_objects['mainVideo'] ){
                $campaign->library_media()->attach($request->media_objects['mainVideo']['id'],['collection' => 'mainVideo']);
            }
            if( $request->media_objects['galleryImages'] ){
                $campaign->library_media()->attach(Arr::pluck($request->media_objects['galleryImages'],'id'),['collection' => 'galleryImages']);
            }
        }
        

        //breadcrumb
        $bc = saveBreadCrumb($campaign);

        return back()->with('flash',[
            'type' => 'success',
            'message' => 'Kampanya güncellendi.',
            'data' => $campaign
        ]);
    }

    public function delete(Campaign $campaign)
    {
        $this->authorize('delete', Campaign::class);

        $campaign->delete();
        return back();
    }

    public function restore(Campaign $campaign)
    {
        $this->authorize('restore', Campaign::class);

        $campaign->restore();
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        $this->authorize('forceDelete', Campaign::class);

        $campaign->forceDelete();
        return back();
    }


    public function bulkDelete(Request $request)
    {
        $this->authorize('delete', Campaign::class);

        $contents = Campaign::whereIn('id',$request->items)->get();
        $contents->each->delete();
        return back()->with('flash',[
            'type' =>'success',
            'message' => "Seçilen içerikler silindi!"
        ]);
    }

    public function bulkRestore(Request $request)
    {
        $this->authorize('restore', Campaign::class);

        $contents = Campaign::onlyTrashed()->whereIn('id',$request->items)->get();
        $contents->each->restore();
        return back()->with('flash',[
            'type' =>'success',
            'message' => "Seçilen içerikler geri alındı!"
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('forceDelete', Campaign::class);

        $contents = Campaign::onlyTrashed()->whereIn('id',$request->items)->get();
        $contents->each->forceDelete();
        return back()->with('flash',[
            'type' =>'success',
            'message' => "Seçilen içerikler silindi!"
        ]);
    }

}
