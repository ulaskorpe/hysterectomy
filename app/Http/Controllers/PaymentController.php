<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use Jackiedo\Cart\Cart;
use App\Models\CartTemp;
use App\Models\CouponGroup;
use App\Models\OrderStatus;
use App\Models\UserAddress;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Http\Libraries\Parasut;
use App\Mail\OrderReceivedMail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminOrderReceivedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Libraries\IyzicoLibrary;
use App\Mail\ProductTypeDownloadFileEmail;

class PaymentController extends Controller
{
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
    
    public function index(Request $request){

        seo()->title(__('Ödeme'));

        if( Auth::check() ){
            
            return view('e-commerce.payment-auth',[
                'cart' => $this->cart->getDetails(),
                'user' => $request->user(),
                'addresses' => $request->user()->addresses,
            ]);

        } else {

            return view('e-commerce.payment',[
                'cart' => $this->cart->getDetails()
            ]);

        }

    }

    public function guest(Request $request){

        if( Auth::check() ){
            
            return redirect()->route('payment.index');

        }

        seo()->title(__('Ödeme'));

        return view('e-commerce.payment-guest',[
            'cart' => $this->cart->getDetails()
        ]);

    }


    public function payStart(Request $request){

        $shipping = [];
        $billing = [];
        $billing_extra = [];

        if( Auth::check() ){
            
            $request->validate(
                [
                    'shipping_id' => 'required|numeric',
                    'billing_id' => 'required|numeric',
                    'mesafeli' => 'required',
                    'onbilgilendirme' => 'required',
                ],
                [
                    'shipping_id' => __('Teslimat adresi seçilmelidir.'),
                    'billing_id' => __('Fatura adresi seçilmelidir.'),
                    'mesafeli' => __('Mesafeli satış sözleşmesi onayı verilmelidir.'),
                    'onbilgilendirme' => __('Önbilgilendirme formu onayı verilmelidir.'),
                ]
            );

            $shipping_address = UserAddress::find((int)$request->shipping_id);
            $billing_address = UserAddress::find((int)$request->billing_id);

            if( !$shipping_address || !$billing_address ){

                return back()->with('error',__('Seçilen adres bulunamadı!'));

            }

            $shipping = [
                'name' => $shipping_address->name,
                'phone' => $shipping_address->phone,
                'email' => $shipping_address->email,
                'address' => $shipping_address->address,
                'county' => $shipping_address->county,
                'state' => $shipping_address->state->name,
                'country' => $shipping_address->country->native,
            ];

            $billing = [
                'name' => $billing_address->name,
                'phone' => $billing_address->phone,
                'email' => $billing_address->email,
                'address' => $billing_address->address,
                'county' => $billing_address->county,
                'state' => $billing_address->state->name,
                'country' => $billing_address->country->native,
            ];

            $billing_extra = [
                'billing_type' => $billing_address->billing_type,
                'tc_no' => $billing_address->tc_no,
                'company_name' => $billing_address->company_name,
                'vd' => $billing_address->vd,
                'vkn' => $billing_address->vkn,
                'e_fatura' => $billing_address->e_fatura,
            ];

        } else {

            $request->validate(
                [
                    'shipping.name' => 'required|regex:/^\S+\s+\S+/',
                    'shipping.email' => 'required|email',
                    'shipping.phone' => 'required',
                    'shipping.country' => 'required',
                    'shipping.state' => 'required',
                    'shipping.county' => 'required',
                    'billing_type' => 'required',
                    'tc_no' => [
                        Rule::requiredIf($request->billing_type == 'Bireysel'),
                        function ($attribute, $value, $fail) use ($request) {
                            if ($request->billing_type == 'Bireysel') {
                                if (!is_numeric($value)) {
                                    return $fail('TC kimlik numarası sadece sayılardan oluşmalıdır.');
                                }
                                if (strlen($value) !== 11) {
                                    return $fail('TC kimlik numarası tam olarak 11 haneli olmalıdır.');
                                }
                            }
                        }
                    ],
                    'company_name' => Rule::requiredIf($request->billing_type == 'Kurumsal'),
                    'vd' => Rule::requiredIf($request->billing_type == 'Kurumsal'),
                    'vkn' => Rule::requiredIf($request->billing_type == 'Kurumsal'),
                    'billing.name' => [
                        Rule::requiredIf(!$request->has('billing_same')),
                        function ($attribute, $value, $fail) use ($request) {
                            if (!$request->has('billing_same')) {
                                if (empty(trim($value))) {
                                    return $fail('İsim alanı boş olamaz.');
                                }
    
                                if (!preg_match('/^\S+\s+\S+/', $value)) {
                                    return $fail('İsim en az 2 kelimeden oluşmalıdır.');
                                }
                            }
                        }
                    ],
                    'billing.email' => [
                        Rule::requiredIf(!$request->has('billing_same')),
                        function ($attribute, $value, $fail) use ($request) {
                            if (!$request->has('billing_same')) {
                                if (empty(trim($value))) {
                                    return $fail('Fatura adresi email alanı boş olamaz.');
                                }
                                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    return $fail('Geçerli bir email adresi girin.');
                                }
                            }
                            
                        },
                    ],
                    'billing.phone' => [Rule::requiredIf(!$request->has('billing_same'))],
                    'billing.country' => [Rule::requiredIf(!$request->has('billing_same'))],
                    'billing.state' => [Rule::requiredIf(!$request->has('billing_same'))],
                    'billing.county' => [Rule::requiredIf(!$request->has('billing_same'))],
                    'mesafeli' => 'required',
                    'onbilgilendirme' => 'required',
                ],
                [
                    'shipping.name.regex' => 'İsim alanı en az 2 kelime olmalıdır.',
                    'mesafeli' => __('Mesafeli satış sözleşmesi onayı verilmelidir.'),
                    'onbilgilendirme' => __('Önbilgilendirme formu onayı verilmelidir.'),
                ]
            );

            $shipping = [
                'name' => $request->shipping['name'],
                'phone' => $request->shipping['phone'],
                'email' => $request->shipping['email'],
                'address' => $request->shipping['address'],
                'county' => $request->shipping['county'],
                'state' => $request->shipping['state'],
                'country' => $request->shipping['country'],
            ];

            $billing_details = $request->billing_same ? $request->shipping : $request->billing;

            $billing = [
                'name' => $billing_details['name'],
                'phone' => $billing_details['phone'],
                'email' => $billing_details['email'],
                'address' => $billing_details['address'],
                'county' => $billing_details['county'],
                'state' => $billing_details['state'],
                'country' => $billing_details['country'],
            ];

            $billing_extra = [
                'billing_type' => $request->billing_type,
                'tc_no' => $request->tc_no,
                'company_name' => $request->company_name,
                'vd' => $request->vd,
                'vkn' => $request->vkn,
                'e_fatura' => $request->e_fatura,
            ];

        }

        $this->cart->setExtraInfo([
            'shipping' => $shipping,
            'billing' => $billing,
            'billing_extra' => $billing_extra,
            'notes' => $request->notes
        ]);

        //Odeme islemi basladigi anda temp cart olusturalim.
        $sessionId = session()->getId();

        $create_cart_temp = CartTemp::updateOrCreate(
            [
                'session_id' => $sessionId,
            ],
            [
                'session_id' => $sessionId,
                'cart_options' => self::prepareCartOptionsForOrder($this->cart),
                'user_id' => Auth::check() ? Auth::id() : 0,
            ]
        );


        //burada tekrar stok kontrolu
        if( checkCurrentStocks($this->cart) > 0 ){
            return back()->with('error','Sepetinizdeki ürünlerden bazıları için artık yeterli stok bulunmuyor.');
            die();
        }


        if( $request->payment_type == 'free' && $this->cart->getTotal() == 0 ){

            $order = self::createOrder($create_cart_temp->cart_options, 'free');

            if( $order ){

                activity()->performedOn($order)->log(__('Sipariş oluşturuldu.'));

                //siparis olusturuldu. sepeti temizle
                $this->cart->destroy();
                $create_cart_temp->delete();

                //mailleri gonder
                try {
                    
                    $customer_mail = Mail::to($order->cart_details['extra_info']['billing']['email'])->send(new OrderReceivedMail($order));
                    $admin_email = Mail::to(config('app-ea.app_order_email'))->send(new AdminOrderReceivedMail($order));

                } catch (\Throwable $th) {
                    Log::error("Siparis e-postalari gonderilirken hata olustu. Siparis ID: ".$order->id);
                    Log::error($th->getMessage());
                }

            }

            return redirect()->route('payment.pay.success', [$order,'email' => $order->cart_details['extra_info']['shipping']['email']]);

            die();

        }

        if( $request->payment_type == 'transfer' ){

            $order = self::createOrder($create_cart_temp->cart_options, 'transfer');

            if( $order ){

                activity()->performedOn($order)->log(__('Sipariş oluşturuldu.'));

                //siparis olusturuldu. sepeti temizle
                $this->cart->destroy();
                $create_cart_temp->delete();

                //mailleri gonder
                try {
                    
                    $customer_mail = Mail::to($order->cart_details['extra_info']['billing']['email'])->send(new OrderReceivedMail($order));
                    $admin_email = Mail::to(config('app-ea.app_order_email'))->send(new AdminOrderReceivedMail($order));

                } catch (\Throwable $th) {
                    Log::error("Siparis e-postalari gonderilirken hata olustu. Siparis ID: ".$order->id);
                    Log::error($th->getMessage());
                }

            }

            return redirect()->route('payment.pay.success', [$order,'email' => $order->cart_details['extra_info']['shipping']['email']]);

            die();

        }


        if( $request->payment_type == 'iyzico' ){

            $sessionId = session()->getId();

            $iyzico_scripts = IyzicoLibrary::generateScript($this->cart,$sessionId);

            Log::debug($iyzico_scripts);

            if($iyzico_scripts['status'] == 'error') {
                return back()->with('error',__('İşlem sırasında hata oluştu!'));
            };

            return view('e-commerce.payment-iyzico',[
                'cart_details' => $this->cart->getDetails(),
                'script' => $iyzico_scripts['script']
            ]);

        }        


    }


    public function iyzicoCallback(Request $request){

        $token = $request->token;
        $sessionId = $request->sid;

        if( !$token && !$sessionId ){
            return redirect("/")->with('error','İşlem sırasında hata oluştu! #1');
        }

        $currentSessionId = session()->getId();

        $temp_cart = CartTemp::where('session_id',$sessionId)->first();
        
        if( $sessionId != $currentSessionId){

            $user_id = $temp_cart->user_id;

            if( !Auth::check() && $user_id > 0 ){

                $member = User::find($user_id);
                Auth::login($member);

            }

        }
        
        $payment_status = IyzicoLibrary::checkPaymentStatus($token);

        if($payment_status['status'] == 'error') {
            return back()->with('error',$payment_status['message']);
            die();
        };

        $order = self::createOrder($temp_cart->cart_options, 'iyzico', $token);

        if( $order ){

            activity()->performedOn($order)->log(__('Sipariş oluşturuldu.'));

            //siparis olusturuldu. sepeti temizle. temp sil 
            $this->cart->destroy();
            $temp_cart->delete();

            //mailleri gonder
            try {
                    
                $customer_mail = Mail::to($order->cart_details['extra_info']['billing']['email'])->send(new OrderReceivedMail($order));
                $admin_email = Mail::to(config('app-ea.app_order_email'))->send(new AdminOrderReceivedMail($order));

            } catch (\Throwable $th) {
                Log::error("Siparis e-postalari gonderilirken hata olustu. Siparis ID: ".$order->id);
                Log::error($th->getMessage());
            }

        }

        return redirect()->route('payment.pay.success', [$order,'email' => $order->cart_details['extra_info']['shipping']['email']]);

        die;

    }


    public function orderSuccess(Request $request, Order $order){

        if( !$request->email || $order->cart_details['extra_info']['shipping']['email'] != $request->email){
            return redirect("/");
        }

        return view('e-commerce.payment-success',[
            'order' => $order->loadMissing(['status','coupon'])
        ]);

    }

    public static function prepareCartOptionsForOrder(Cart $cart){

        return [
            'getDetails' => $cart->getDetails(),
            'getItemsSubtotal' => $cart->getItemsSubtotal(),
            'getTotal' => $cart->getTotal(),
            'getActions' => $cart->getActions(),
            'getItems' => $cart->getItems()
        ];

    }

    public static function createOrder(array $cartOptions,$payment_type,$iyzico_token = null) { 
        
        //once order kaydini olusturalim.
        $order = new Order;
        $order->code = generateOrderCode();
        $order->payment_type = $payment_type;
        $order->iyzico_token = $iyzico_token;
        $order->cart_details = $cartOptions['getDetails'];
        $order->subtotal = $cartOptions['getItemsSubtotal'];
        $order->total = $cartOptions['getTotal'];

        //kupon ve hediye ceki indirimleri
        foreach ($cartOptions['getDetails']['applied_actions'] as $key => $action) {

            if( $action['group'] === 'CouponDiscount' ){

                $coupon = Coupon::find($action['extra_info']['coupon_details']['id']);

                //kuponun kullanim adetini artir
                $coupon->increment('used_count');

                //kupon as_voucher ise used_amount kismini artir.
                if( $coupon->as_voucher ){

                    $coupon->used_amount = $coupon->used_amount + abs($action['value']);
                    $coupon->save();
                }
                

                $order->coupon_id = $coupon->id;

            }

            if( $action['group'] === 'CampaignDiscount' ){

                $order->campaign_id = $action['extra_info']['campaign_details']['id'];

            }
        }

        //status secicez
        $o_status = OrderStatus::when($payment_type == 'transfer', function($query){
            $query->where('for_wait_payment',1);
        })->when($payment_type == 'iyzico', function($query){
            $query->where('for_new',1);
        })->first();

        $order->order_status_id = $o_status->id;

        $order->save();

        $product_types = [];

        //simdi her bir urun icin stok guncellemesi ve order_count guncellemesi yapalim. ayrica email tipinde felan ise o isleri de halledelim.
        //order_products tablosuna da eklemeleri yapalim.
        foreach ($cartOptions['getDetails']['items'] as $p => $item) {
            
            $product_types[] = $item['extra_info']['product_type_id'];

            $product = Product::withoutGlobalScopes()->where('id',$item['extra_info']['product_details']['id'])->first();

            $product->increment('order_count',$item['quantity']);

            if( $product->stock ){
                $product->decrement('stock',$item['quantity']);
            }

            $variant = $item['extra_info']['variant_details'] ? 
            ProductVariant::withoutGlobalScopes()->where('id',$item['extra_info']['variant_details']['id'])->first() 
            : null;

            if( $variant && $variant->stock){
                $variant->decrement('stock',$item['quantity']);
            }

            //order_products tablosu isleri
            //discount raporlar icin onemli. discount default olarak bu tabloda 0
            //indirimleri her durumda sepet toplamından dusuyoruz notunu buraya da duseyim
            //sepette sadece 1 tip urun varsa urun raporlari icin kullanabiliriz. genel sepet indirimini ekleyelim.
            //sepette birden fazla kalem urun varsa ve sepet indirimi apply_cart alani false ise, oradaki item_id ile bu item id ayni ise sepet indirimini bu urune ekleyelim.
            //sepette birden fazla urun varsa ve yapilan indirim urun bazli degilse, hangi urun icin indirim yapacagimizi bilmedigimizden discount 0 kalsin.
            $order_product = new OrderProduct();
            $order_product->order_id = $order->id;
            $order_product->product_id = $product->id;
            $order_product->product_variant_id = $variant ? $variant->id : null;
            $order_product->count = $item['quantity'];
            $order_product->price = $item['subtotal'];
            $order_item_discount = 0;

            //herseyden once siparisde indirim var mi? yoksa zaten 0 dan devam
            if( $order->subtotal > $order->total ){

                if( count($cartOptions['getDetails']['items']) === 1 ){

                    $order_item_discount = $order->subtotal - $order->total;

                } else {

                    $urunIcinYapilanIndirimler = [];
                    //indirimler arasinda item_id bu urun_id si olan var mi?
                    foreach ($cartOptions['getDetails']['applied_actions'] as $key => $action) {

                        if( isset($action['extra_info']['item_id']) && $action['extra_info']['item_id'] ===  $item['id'] ){
                            $urunIcinYapilanIndirimler[] = abs($action['amount']);
                        }

                    }

                    $order_item_discount = count($urunIcinYapilanIndirimler) > 0 ? array_sum($urunIcinYapilanIndirimler) : 0;

                }

            }

            $order_product->discount = $order_item_discount;
            $order_product->save();

            
            //simdi urun tipine gore email vs isleri varsa onu yapalim.
            switch ( $product->product_type->after_order_type ) {

                case "download":
                    
                    try {
                    
                        $send_product_mail = Mail::to($order->cart_details['extra_info']['billing']['email'])->send(new ProductTypeDownloadFileEmail($product,$order));
        
                    } catch (\Throwable $th) {
                        Log::error("After order type DOWNLOAD e-postasi gonderilirken hata olustu. Siparis ID: ".$order->id);
                        Log::error($th->getMessage());
                    }

                break;

                default:
                    //
                break;

            }
            
        }

        //urun tiplerini de ekleyelim tamamdir.
        $order->product_types = $product_types;
        $order->save();
        
        $parasut = new Parasut();
        $parasut->faturaOlustur($order); //parasut aktif felan hepsi library de.

        return $order;

    }

}
