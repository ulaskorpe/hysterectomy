<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Coupon;
use App\Models\Product;
use Jackiedo\Cart\Cart;
use App\Models\Campaign;
use App\Models\ProductCustomerAttribute;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class CartController extends Controller
{

    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart->name('default');
    }

    public function index(Request $request)
    {
        //$this->cart->destroy();
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cartDetails' => $this->cart->getDetails(),
                'cartHtml' => View::make('components.cart-mini', [
                    'cart' => $this->cart->getDetails(),
                    'removeButtons' => true
                ])->render()
            ]);
        }

        seo()->title(__('Sepetim'));

        return view('e-commerce.cart', [
            'cart' => $this->cart->getDetails(),
        ]);
    }


    public function addItem(Request $request)
    {

        $directToPayment = false;

        if( isset($request->direct_to_payment) && $request->direct_to_payment === 'true' ){
            $directToPayment = true;
        }

        if( $directToPayment ){
            $this->cart->destroy();
        }

        //dd($request->all());
        $product = null;
        $variant = null;

        if ($request->is_variant == 1) {

            $request->validate(
                [
                    'variant_id' => 'required',
                    'product_id' => 'required'
                ],
                [
                    'variant_id.required' => 'Lütfen seçeneklerden size uygun olanı seçiniz'
                ]
            );

            $variant = ProductVariant::where('id', $request->variant_id)->where('product_id', $request->product_id)->first();

            if (!$variant) {
                return back()->with('error', 'Ürün seçenek grubu bulunamadı!');
            }

            $product = $variant->product;

            if ($variant->option_group->stock_usage && $request->quantity > $variant->stock) {
                return back()->with('error', __('Yeterli stok bulunmamaktadır.'));
            }

        } else {

            $request->validate([
                'product_id' => 'required'
            ]);

            $product = Product::withoutGlobalScopes()->find($request->product_id);
            if (!$product) {
                return back()->with('error', 'Ürün bulunamadı.');
            }

            if ($product->product_type->stock_usage && $request->quantity > $product->stock) {
                return back()->with('error', __('Yeterli stok bulunmamaktadır.'));
            }
        }

        $selections = [];

        //requestten gelen selectionlari ekleyelim.
        if ($request->selections) {
            foreach ($request->selections as $sel) {
                $selections[] = $sel;
            }
        }

        //customer attribıtes geldiyse onlari halledelim.
        $customer_attributes = [];

        if( $request->customer_attributes ){

            foreach ($request->customer_attributes as $key => $value) {
                
                $custAttr = ProductCustomerAttribute::find($key);

                if( $custAttr && !empty($value) && $value !== null ){

                    $customer_attributes[] = [
                        'id' => $custAttr->id,
                        'label' => $custAttr->name,
                        'value' => $value
                    ];

                }

            }

        }

        $cart_price = $variant && $variant->price ? $variant->price : $product->product_price;

        $data = [
            'id'       => $variant ? $variant->id : $product->id,
            'title'    => $product->name,
            'quantity' => $request->quantity ?? 1,
            'price'    => $cart_price->price,
            'extra_info' => [
                'attributes' => $product->attributes,
                'main_image' => $product->media_objects['mainImage'],
                'variant_details' => $variant ? [
                    'id' => $variant->id,
                    'uuid' => $variant->uuid,
                    'price' => $variant->price ? [
                        'id' => $variant->price->id,
                        'price' => $variant->price->price,
                        'discount' => $variant->price->discount,
                        'currency_id' => $variant->price->currency_id,
                        'final_price' => $variant->price->final_price,
                    ] : null,
                    'stock' => $variant->stock,
                    'option_group' => $variant->option_group ? [
                        'id' => $variant->option_group->id,
                        'name' => $variant->option_group->name,
                    ] : null,
                    'option_values' => $variant->option_values,
                    'product_option_group_id' => $variant->product_option_group_id,
                    'main_image' => $variant->media_objects['mainImage'],
                    'incart' => $variant->incart
                ] : null,
                'selections' => count($selections) > 0 ? $selections : null,
                'product_id' => $product->id,
                'product_details' => [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'tax' => $product->tax,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'uuid' => $product->uuid,
                    'product_price' => $product->product_price ? [
                        'id' => $product->product_price->id,
                        'price' => $product->product_price->price,
                        'discount' => $product->product_price->discount,
                        'currency_id' => $product->product_price->currency_id,
                        'final_price' => $product->product_price->final_price,
                    ] : null,
                    'stock' => $product->stock,
                    'product_type' => $product->product_type ? [
                        'id' => $product->product_type->id,
                        'name' => $product->product_type->name,
                        'product_option_group_id' => $product->product_type->product_option_group_id,
                    ] : null,
                    'tax_id' => $product->tax_id,
                    'additional' => $product->additional,
                    'attributes' => $product->attributes,
                    'main_image' => $product->media_objects['mainImage'],
                    'incart' => $product->incart
                ],
                'product_type_id' => $product->product_type_id,
                'tax' => $product->tax,
                'customer_attributes' => $customer_attributes,
                'date_time' => [
                    'added_at' => time(),
                ]
            ],
            'options' => []
        ];

        $item = $this->cart->addItem($data);

        //tax ayrica eklenecekse uygulayalim.
        if( config('app-ea.app_product_tax_include') === false && $product->tax ){

            $item->applyAction([
                'group'      => 'Tax',
                'id'         => $product->tax->id,
                'title'      => $product->tax->name,
                'value'      => $product->tax->percentage . '%',
                'extra_info' => [
                    'description' => '',
                    'coupon_details' => null
                ]
            ]);
            
        }

        //indirim varsa uygula
        if ($cart_price->discount) {
            $discountType = $cart_price->discount->discount_type;
            $discountValue = $cart_price->discount->value;

            $item->applyAction([
                'group'      => 'ProductDiscount',
                'id'         => $cart_price->discount->id,
                'title'      => 'İndirimli Ürün',
                'value'      => $discountType == 'percentage' ? '-' . $discountValue . '%' : -$discountValue,
                'extra_info' => [
                    'description' => '',
                    'coupon_details' => null
                ]
            ]);
        }

        if ($request->wantsJson()) {

            self::refreshCargoActions($this->cart);

            return response()->json([
                'success' => true,
                'message' => $product->name . ' sepetinize eklendi.',
                'productButtons' => '#product-' . $product['uuid'] . '-buttons',
                'productButtonsHtml' => View::make('components.cards.product-card-buttons', [
                    'product' => $product
                ])->render()
            ]);
        }

        self::refreshCargoActions($this->cart);

        if( $directToPayment ){
            return redirect()->route('payment.index');
        }

        return back()->with('itemAddedToCart', $product->name . ' sepetinize eklendi.');
    }

    public function removeItem(Request $request, $itemHash)
    {

        $item = $this->cart->getItem($itemHash);
        $cartProduct = $item->getDetails()->extra_info->product_details;

        $this->cart->removeItem($itemHash);

        self::refreshCampaignAndCouponActions($this->cart);
        self::refreshCargoActions($this->cart);
        self::refreshIfCartEmpty($this->cart);

        $product = Product::find($cartProduct['id']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'productButtons' => '#product-' . $product->id . '-buttons',
                'productButtonsHtml' => View::make('components.cards.product-card-buttons', [
                    'product' => $product
                ])->render()
            ]);
        }

        return back();
    }

    public function changeItemQuantity(Request $request, $itemHash, $quantity)
    {

        $item = $this->cart->getItem($itemHash);

        //onelikle product o an silinmis olabilir.
        $item_id = $item->getId();
        $product_find_id = $item_id;
        $product = null;
        $product_variant = null;

        $stock_error = 0;

        if( $item->getDetails()['extra_info']['variant_details'] ){

            $product_variant = ProductVariant::find($item_id);

            if( !$product_variant ){

                $this->cart->removeItem($itemHash);

                self::refreshCampaignAndCouponActions($this->cart);
                self::refreshCargoActions($this->cart);
                self::refreshIfCartEmpty($this->cart);

                return back()->with('error',__('İlgili seçiminize ait ürün bulunamamıştır.'));

            }

            if( $product_variant->stock != null && $quantity > $product_variant->stock ){
                $stock_error++;
            }

            $product_find_id = $product_variant->product_id;

        }

        $product = Product::find($product_find_id);

        if( !$product ){

            $this->cart->removeItem($itemHash);

            self::refreshCampaignAndCouponActions($this->cart);
            self::refreshCargoActions($this->cart);
            self::refreshIfCartEmpty($this->cart);

            return back()->with('error',__('İlgili seçiminize ait ürün bulunamamıştır.'));

        }
        
        if( !$product_variant && $product->stock != null && $quantity > $product->stock ){
            $stock_error++;
        }

        if( $stock_error > 0 ){
            return back()->with('error',__('Bu üründen alabileceğiniz maksimum adet sepetinizde bulunuyor..'));
        }

        $this->cart->updateItem($itemHash, [
            'quantity'      => $quantity
        ]);

        self::refreshCampaignAndCouponActions($this->cart);
        self::refreshCargoActions($this->cart);
        self::refreshIfCartEmpty($this->cart);

        if ($request->wantsJson()) {

            return response()->json([
                'success' => true,
                'productButtons' => '#product-' . $product['uuid'] . '-buttons',
                'productButtonsHtml' => View::make('components.cards.product-card-buttons', [
                    'product' => $product
                ])->render()
            ]);
        }

        return back();
    }



    public function destroy()
    {
        $this->cart->destroy();
    }


    public function applyCouponCode(Request $request)
    {

        $request->validate([
            'code' => 'required',
        ]);

        $coupon = Coupon::whereHas('group',function($query){
            $query->where('is_active',true);
        })->where('code', $request->code)->first();

        if (!$coupon) {
            return back()->with('error', __('Geçersiz kupon kodu kullandınız.'));
        }

        $today = today();
        $start_date = Carbon::parse($coupon->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($coupon->end_date)->format('Y-m-d');

        if (!$coupon->as_voucher && $coupon->used_count >= $coupon->usage_count) {
            return back()->with('error', __('Kupon daha önceden kullanılmış.'));
        }

        if ($coupon->as_voucher && $coupon->used_amount >= $coupon->discount) {
            return back()->with('error', __('Hediye çeki tutarının tamamı kullanılmış.'));
        }

        if ($today < $start_date) {
            return back()->with('error', __('Kuponun kullanılabileceği ilk tarih: ' . $start_date));
        }

        if ($today > $end_date) {
            return back()->with('error', __('Kuponun son kullanım tarihi: ' . $end_date));
        }


        $applyCouponDiscount = self::applyCouponCodeDiscount($coupon, $this->cart);

        if ($applyCouponDiscount) {
            return back()->with($applyCouponDiscount['type'], $applyCouponDiscount['message']);
        }

        return back()->with('error', __('Kupon uygulanırken hata oluştu!'));
    }

    public function removeCouponCode(Request $request)
    {

        $request->validate([
            'hash' => 'required',
        ]);

        $action = $this->cart->getAction($request->hash);
        //$coupon = Coupon::find($action->getExtraInfo()['coupon_details']['id']);
        //$coupon->decrement('used_count');

        $this->cart->removeAction($request->hash);

        self::refreshCargoActions($this->cart);

        return back()->with('success', __('Kupon kaldırıldı.'));
    }



    public function applyCampaign(Request $request)
    {

        //Once eklenmis kampanya varsa kaldiralim

        $actions = $this->cart->getActions();

        foreach ($actions as $key => $action) {

            if ($action->getGroup() == 'CampaignDiscount') {

                $this->cart->removeAction($action->getHash());
            }
        }

        $campaign = Campaign::where('id', $request->campaign_id)->first();

        if (!$campaign) {
            return back()->with('error', __('Kampanya bulunamadı.'));
        }

        if (!$campaign->isUsable()) {
            return back()->with('error', __('Kampanya şartları tamamen karşılanmıyor.'));
        }

        $applyCampaignDiscount = self::applyCampaignDiscount($campaign, $this->cart);

        if ($applyCampaignDiscount) {
            return back()->with($applyCampaignDiscount['type'], $applyCampaignDiscount['message']);
        }

        return back()->with('error', __('Kampanya uygulanırken hata oluştu!'));
    }

    public function removeCampaign(Request $request)
    {

        $request->validate([
            'hash' => 'required',
        ]);

        $action = $this->cart->getAction($request->hash);

        $this->cart->removeAction($request->hash);

        self::refreshCargoActions($this->cart);

        return back()->with('success', __('Kampanya kaldırıldı.'));
    }


    public static function applyCouponCodeDiscount(Coupon $coupon, Cart $cart)
    {

        $coupon->loadMissing('product_types:id,name,slug,product_option_group_id');

        if ($coupon->apply_cart) {

            $discount = $coupon->discount;

            if ($coupon->as_voucher && $coupon->type == 'fixed') {

                $cartTotal = $cart->getTotal();
                $usableAmount = $coupon->discount - $coupon->used_amount;
                $discount = - ($usableAmount >= $cartTotal ? $cartTotal : $usableAmount);
                
            } else if ($coupon->type == 'fixed') {
                $discount = -$discount;
            } else if ($coupon->type == 'percentage') {
                $discount = '-' . $coupon->discount . '%';
            }

            $cart->applyAction([
                'group'      => 'CouponDiscount',
                'id'         => $coupon->id,
                'title'      => $coupon->code,
                'value'      => $discount,
                'extra_info' => [
                    'description' => '',
                    'coupon_details' => [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'type' => $coupon->type,
                        'user_id' => $coupon->user_id,
                        'discount' => $coupon->discount,
                        'end_date' => $coupon->end_date,
                        'apply_cart' => $coupon->apply_cart,
                        'as_voucher' => $coupon->as_voucher,
                        'start_date' => $coupon->start_date,
                        'used_count' => $coupon->used_count,
                        'usage_count' => $coupon->usage_count,
                        'used_amount' => $coupon->used_amount,
                        'company_amount' => $coupon->company_amount,
                        'coupon_group_id' => $coupon->coupon_group_id,
                        'product_types' => $coupon->product_types->map(function ($p) {
                            return [
                                'id' => $p->id,
                                'name' => $p->name,
                                'product_option_group_id' => $p->product_option_group_id,
                            ];
                        })
                    ],
                    'as_voucher' => $coupon->as_voucher,
                    'apply_cart' => true,
                    'item_id' => null //asagida tekil urun icin indirim olursa indirim yapilan product veya variant in id sini koyacagiz ki, raporlar kolaylassin.
                ]
            ]);


            self::refreshCargoActions($cart);

            return [
                'type' => 'success',
                'message' => __('İndirim başarıyla uygulandı')
            ];
        }

        $product_type_ids = $coupon->product_types()->pluck('id')->toArray();
        $addable = [];

        foreach ($cart->getItems() as $key => $item) {

            if (in_array($item->get('extra_info')['product_type_id'], $product_type_ids)) {

                $addable[] = $item;
            }
        }

        if (count($addable) == 0) {

            return [
                'type' => 'error',
                'message' => __('Kuponun geçerli olduğu ürünler sepetinizde bulunmuyor.')
            ];
        }

        // foreach ($addable as $a => $item) {
        //     $item->applyAction([
        //         'group'      => 'CouponDiscount',
        //         'id'         => $coupon->id,
        //         'title'      => $coupon->code,
        //         'value'      => $coupon->type == 'percentage' ? '-'.$coupon->discount.'%' : -$coupon->discount,
        //         'extra_info' => [
        //             'description' => '',
        //             'coupon_details' => $coupon
        //         ]
        //     ]);
        // }

        //sadece uygun ilk urun icin indirim yapalim. Ancak indirimi tum sepete uygulayacagiz. indirim miktari icin urunu kullaniyoruz. raporlari kolaylastirmak icin ise, order products kismina urun icin indirim ise onu yazacagiz.
        $discount = $coupon->discount;
        $itemTotal = $addable[0]->getSubtotal();

        if ($coupon->as_voucher && $coupon->type == 'fixed') {

            $usableAmount = $coupon->discount - $coupon->used_amount;
            $discount = - ($usableAmount >= $itemTotal ? $itemTotal : $usableAmount);

        } else if ($coupon->type == 'fixed') {

            $discount = -$discount;

        } else if ($coupon->type == 'percentage') {

            //sadece ilk urun icin yuzdesel tutari hesaplayacagiz.
            //ayni urunden birden fazla varsa ve kuponun kullanim hakki 1 kaldiysa urunlerden birinin fiyati uzerinden discount yapalim.
            $indirimde_kullanilacak_tutar = $itemTotal;

            if (($coupon->usage_count - $coupon->used_count) === 1 && $addable[0]->getQuantity() > 1) {
                $indirimde_kullanilacak_tutar = $itemTotal / $addable[0]->getQuantity();
            }
            $indirim_tutari = ($indirimde_kullanilacak_tutar * $coupon->discount) / 100;

            $discount = -$indirim_tutari;
        }

        $cart->applyAction([
            'group'      => 'CouponDiscount',
            'id'         => $coupon->id,
            'title'      => $coupon->code,
            'value'      => $discount,
            'extra_info' => [
                'description' => '',
                'coupon_details' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'user_id' => $coupon->user_id,
                    'discount' => $coupon->discount,
                    'end_date' => $coupon->end_date,
                    'apply_cart' => $coupon->apply_cart,
                    'as_voucher' => $coupon->as_voucher,
                    'start_date' => $coupon->start_date,
                    'used_count' => $coupon->used_count,
                    'usage_count' => $coupon->usage_count,
                    'used_amount' => $coupon->used_amount,
                    'company_amount' => $coupon->company_amount,
                    'coupon_group_id' => $coupon->coupon_group_id,
                    'product_types' => $coupon->product_types->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'product_option_group_id' => $p->product_option_group_id,
                        ];
                    })
                ],
                'as_voucher' => $coupon->as_voucher,
                'apply_cart' => false,
                'item_id' => $addable[0]->getId()
            ]
        ]);

        self::refreshCargoActions($cart);

        return [
            'type' => 'success',
            'message' => __('İndirim geçerli olan ürünler için başarıyla uygulandı')
        ];
    }

    public static function applyCampaignDiscount(Campaign $campaign, Cart $cart)
    {

        $campaign->loadMissing('product_types:id,name,slug,product_option_group_id');

        if ($campaign->apply_cart) {

            $discount = $campaign->discount;

            if ($campaign->type == 'fixed') {
                $discount = -$discount;
            } else if ($campaign->type == 'percentage') {
                $discount = '-' . $campaign->discount . '%';
            }

            $cart->applyAction([
                'group'      => 'CampaignDiscount',
                'id'         => $campaign->id,
                'title'      => $campaign->name,
                'value'      => $discount,
                'extra_info' => [
                    'description' => '',
                    'campaign_details' => $campaign
                ]
            ]);


            self::refreshCargoActions($cart);

            return [
                'type' => 'success',
                'message' => __('Kampanya başarıyla uygulandı')
            ];
        }

        $product_type_ids = $campaign->product_types()->pluck('id')->toArray();
        $addable = [];

        foreach ($cart->getItems() as $key => $item) {
            if (in_array($item->get('extra_info')['product_type_id'], $product_type_ids)) {
                $addable[] = $item;
            }
        }

        if (count($addable) == 0) {
            return [
                'type' => 'error',
                'message' => __('Kampanyanın geçerli olduğu ürünler sepetinizde bulunmuyor.')
            ];
        }

        //sadece uygun ilk urun icin indirim yapalim.
        $discount = $campaign->discount;
        $itemTotal = $addable[0]->getSubtotal();

        if ($campaign->type == 'fixed') {

            $discount = -$discount;

        } else if ($campaign->type == 'percentage') {

            //sadece ilk urun icin yuzdesel tutari hesaplayacagiz.
            $indirim_tutari = ($itemTotal * $discount) / 100;

            $discount = -$indirim_tutari;
        }

        $cart->applyAction([
            'group'      => 'CampaignDiscount',
            'id'         => $campaign->id,
            'title'      => $campaign->name,
            'value'      => $discount,
            'extra_info' => [
                'description' => '',
                'campaign_details' => $campaign
            ]
        ]);

        self::refreshCargoActions($cart);

        return [
            'type' => 'success',
            'message' => __('Kampanya geçerli olan ürünler için başarıyla uygulandı')
        ];
    }


    public static function refreshCampaignAndCouponActions(Cart $cart)
    {

        //sepetteki adet degistigi icin kupon ve kampanyalari kaldirip mevcut haliyle tekrar uygulayalim.
        $actions = $cart->getActions();

        if (count($actions) === 0) {
            return true;
        }

        foreach ($actions as $key => $action) {

            if ($action->getGroup() == 'CampaignDiscount') {

                $campaign = Campaign::find($action->getId());
                $cart->removeAction($action->getHash());

                self::applyCampaignDiscount($campaign, $cart);
            }

            if ($action->getGroup() == 'CouponDiscount') {

                $coupon = Coupon::find($action->getId());
                $cart->removeAction($action->getHash());

                self::applyCouponCodeDiscount($coupon, $cart);
            }
        }
    }

    public static function refreshCargoActions(Cart $cart)
    {

        $actions = $cart->getActions();

        //Once kargo bilgilerini tamamen kaldiralim
        foreach ($actions as $key => $action) {

            if ($action->getGroup() == 'CargoFree' || $action->getGroup() == 'CargoPrice') {

                $cart->removeAction($action->getHash());
            }
        }

        //Simdi once kargo varsa kargo ucretini ekleyelim.
        $cargo = Cargo::first();

        if ($cargo) {

            $cart->applyAction([
                'group'      => 'CargoPrice',
                'id'         => $cargo->id,
                'title'      => $cargo->name,
                'value'      => $cargo->fixed_price,
                'extra_info' => [
                    'description' => '',
                    'cargo_details' => $cargo
                ]
            ]);

            //Simdi toplama gore ucretsiz ise indirim uygulayalim.

            if ($cart->getItemsSubtotal() >= $cargo->free_after) {

                $cart->applyAction([
                    'group'      => 'CargoFree',
                    'id'         => $cargo->id,
                    'title'      => $cargo->free_after . ' üzeri kargo ücretsiz',
                    'value'      => -$cargo->fixed_price,
                    'extra_info' => [
                        'description' => '',
                        'cargo_details' => $cargo
                    ]
                ]);
            }
        }
    }

    public static function refreshIfCartEmpty(Cart $cart)
    {

        if (count($cart->getItems()) === 0) {

            $cart->clearActions();
        }
    }
}
