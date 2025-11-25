<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderStatus;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Exports\ExportOrders;
use App\Mail\OrderUpdatedMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $this->authorize('viewAny',Order::class);

        $orders_query = QueryBuilder::for(Order::class)
        ->with(['status','coupon','user','campaign'])
        ->when($request->input('search'), function ($query, $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('cart_details','like','%'.$search.'%');
            });
        })
        ->defaultSort('-created_at')
        ->allowedFilters([
            AllowedFilter::callback('order_status_id', fn (Builder $query, $value) => $query->whereIn('order_status_id',$value)),
            AllowedFilter::callback('has_coupon', function (Builder $query, $value) {
                if( $value == "1" ){
                    $query->whereNotNull('coupon_id');
                }
                if( $value == "0" ){
                    $query->whereNull('coupon_id');
                }
            }),
            AllowedFilter::scope('start_date'),
            AllowedFilter::scope('end_date'),
            AllowedFilter::callback('product_types', function (Builder $query, $value) {
                $value = array_map('intval', $value);
                $query->where(function($qu) use ($value) {
                    foreach ($value as $id) {
                        $qu->orWhereJsonContains('product_types',$id);
                    }
                });
            }),
            AllowedFilter::callback('products', function (Builder $query, $value) {
                $value = array_map('intval', $value);
                $query->whereHas('products',function (Builder $qu) use ($value) {
                    $qu->whereIn('product_id', $value);
                });
            }),
            AllowedFilter::exact('payment_type'),
        ]);

        if( $request->has('export') && $request->export === "true" ){

            $orders = $orders_query->get();

            $export = new ExportOrders($orders->toArray());

            return Excel::download($export, 'Siparisler.xlsx');

        }

        $orders = $orders_query->paginate(30)->withQueryString();
        
        return Inertia('Order/Index',[
            'orders' => $orders,
            'order_statuses' => OrderStatus::all(),
            'filters' => [
                'order_status_id' => [
                    'label' => 'Sipariş Durumu',
                    'type' => 'select',
                    'options' => OrderStatus::all()->map(function($st){
                        return [
                            'label' => $st->name,
                            'value'=> (string)$st->id // query params string olarak aliyor. vue multiselect de eslesmesi icin string
                        ];
                    }),
                    'value' => $request->filter['order_status_id'] ?? [],
                    'full_width' => true
                ],
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
                'has_coupon' => [
                    'label' => 'Kupon Kullanım',
                    'type' => 'selectSingle',
                    'options' => [
                        [
                            'label' => 'Evet',
                            'value' => '1'
                        ],
                        [
                            'label' => 'Hayır',
                            'value' => '0'
                        ]
                    ],
                    'value' => $request->filter['has_coupon'] ?? "",
                ],
                'product_types' => [
                    'label' => 'Ürün Tipi',
                    'type' => 'select',
                    'options' => ProductType::select('id','name')->get()->map(function($st){
                        return [
                            'label' => $st->name,
                            'value'=> (string)$st->id // query params string olarak aliyor. vue multiselect de eslesmesi icin string
                        ];
                    }),
                    'value' => $request->filter['product_types'] ?? [],
                ],
                'products' => [
                    'label' => 'Ürün',
                    'type' => 'select',
                    'options' => Product::select('id','name')->get()->map(function($st){
                        return [
                            'label' => $st->name,
                            'value'=> (string)$st->id // query params string olarak aliyor. vue multiselect de eslesmesi icin string
                        ];
                    }),
                    'value' => $request->filter['products'] ?? [],
                    'full_width' => true
                ],
                'payment_type' => [
                    'label' => 'Ödeme Yöntemi',
                    'type' => 'selectSingle',
                    'options' => [
                        [
                            'label' => 'Iyzico',
                            'value' => 'iyzico'
                        ],
                        [
                            'label' => 'Havale / EFT',
                            'value' => 'transfer'
                        ]
                    ],
                    'value' => $request->filter['payment_type'] ?? "",
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Order $order)
    {
        $this->authorize('view',$order);

        return Inertia('Order/Show',[
            'order' => $order->loadMissing(['status','coupon','gift_coupons','campaign']),
            'order_statuses' => OrderStatus::all()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Order $order)
    {
        //
    }


    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update',$order);

        $order->fill($request->all());
        $order->save();

        $customer_mail = Mail::to($order->cart_details['extra_info']['billing']['email'])->send(new OrderUpdatedMail($order));

        return back()->with('flash',[
            'type' => 'success',
            'message' => __('Sipariş güncellendi!')
        ]);
    }



    //FE
    public function getOrderSuccess(Request $request, Order $order){

        if( !$request->token){
            return redirect("/")->with('error',__('Token bulunamadı!'));
        }

        $email = Crypt::decryptString($request->token);

        if( $order->cart_details['extra_info']['billing']['email'] != $email){
            return redirect("/")->with('error',__('Geçersiz e-posta adresi.'));
        }

        return view('e-commerce.order-status',[
            'order' => $order->loadMissing(['status','coupon'])
        ]);

    }

    public function postOrderStatus(Request $request)
    {
        
        $request->validate([
            'eposta' => 'required|email',
            'code' => 'required|string'
        ]);

        $order = Order::where('code',$request->code)->first();

        if( !$order ){
            return back()->with('error',__('Sipariş bulunamadı!'));
        }

        $email = $order->cart_details['extra_info']['billing']['email'];

        if( $email != $request->eposta ) {

            return back()->with('error',__('Sipariş kodu ile e-posta adresi eşleşmiyor!'));

        }

        return redirect()->route('order.status.result',[$order,'token' => Crypt::encryptString($email)]);

    }


}
