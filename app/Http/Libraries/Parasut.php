<?php

namespace App\Http\Libraries;

use App\Models\Order;
use App\Models\Parasut as ModelsParasut;
use App\Models\ParasutCustomer;
use App\Settings\GlobalSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Parasut
{
    public function cridentals()
    {

        return [
            'api_url' => config('app-ea.parasut_base_url') . '/' . config('app-ea.parasut_version') . '/' . config('app-ea.parasut_company_id')
        ];
    }

    public function getToken()
    {

        if (session()->has('parasut_token') && now() < session('parasut_token')['expire']) {

            return session('parasut_token')['token'];
        } else {

            $data = array(
                "grant_type" => "password",
                "client_id" => config('app-ea.parasut_client_id'),
                "client_secret" => config('app-ea.parasut_client_secret'),
                "username" => config('app-ea.parasut_username'),
                "password" => config('app-ea.parasut_password'),
                "redirect_uri" => config('app-ea.parasut_redirect_url')
            );

            $send_response = Http::withoutVerifying()->withOptions(["verify" => false])
                ->post(config('app-ea.parasut_base_url') . '/oauth/token', $data);

            $send_response_body = json_decode($send_response->body(), true);

            session()->put('parasut_token', [
                'token' => $send_response_body['access_token'],
                'expire' => now()->addHours(2)
            ]);

            return $send_response_body['access_token'];
        }
    }


    public function createCustomer(array $billing, array $billing_extra)
    {

        $token = $this->getToken();

        try {

            $customer = array(
                'data' =>
                array(
                    'type' => 'contacts',
                    'attributes' => array(
                        'email' => $billing['email'],
                        'name' => $billing['name'], // REQUIRED
                        'contact_type' => $billing_extra['billing_type'] == 'Bireysel' ? 'person' : 'company', // or company
                        'district' => $billing['county'],
                        'city' => $billing['state'],
                        'address' => $billing['address'],
                        'phone' => '+90' . substr(str_replace(' ', '', $billing['phone']), -10),
                        'account_type' => 'customer', // REQUIRED
                        'tax_number' => $billing_extra['tc_no'], // TC no for person
                    )
                ),
            );

            $response = Http::withoutVerifying()
                ->withOptions(["verify" => false])
                ->withToken($token)
                ->post($this->cridentals()['api_url'] . '/contacts', $customer);

            $response_body = json_decode($response->body(), true);

            if ($response->successful()) {

                return [
                    'success' => true,
                    'parasut_customer_data' => $response_body['data']
                ];
            }

            Log::error('Parasut musteri olustururken hata dondu:');
            Log::error(print_r($response_body, true));

            return [
                'success' => false
            ];
        } catch (\Exception $e) {

            Log::error('Parasut musteri olustururken catch error:');
            Log::error($e);

            return [
                'success' => false
            ];
        }
    }


    public function createProduct($id, $name)
    {

        $token = $this->getToken();

        try {
            $product = array(
                'data' => array(
                    'type' => 'products',
                    'attributes' => array(
                        "code" => 'SM-'.$id,
                        "name" => $id . '-' . $name,
                        "vat_rate" => 10,
                    )
                )
            );

            $response = Http::withoutVerifying()
                ->withOptions(["verify" => false])
                ->withToken($token)
                ->post($this->cridentals()['api_url'] . '/products', $product);

            $response_body = json_decode($response->body(), true);

            if ($response->successful()) {

                return [
                    'success' => true,
                    'parasut_product_data' => $response_body['data']
                ];
            }

            Log::error('Parasut urun olustururken hata dondu:');
            Log::error(print_r($response_body, true));

            return [
                'success' => false
            ];
        } catch (\Exception $e) {

            Log::error('Parasut urun olustururken catch error:');
            Log::error($e);

            return [
                'success' => false
            ];
        }
    }


    public function faturaOlustur(Order $order)
    {

        $settings = app(GlobalSettings::class);

        if( !$settings->parasut_active ){
            return false;
        }

        if( $order->total == 0 ){
            return false;
        }

        
        //her siparisi gondermiyoruz. tek cifte gore bakalim.
        if ($order->id % 2 == 0) {
            return false;
        }

        $token = $this->getToken();

        $now = now();
        $today = \Carbon\Carbon::now()->format('Y-d-m');

        $parasut_customer_id = null;

        $name = $order['cart_details']['extra_info']['billing']['name'];
        $address = $order['cart_details']['extra_info']['billing']['address'];
        $email = $order['cart_details']['extra_info']['billing']['email'];
        $phone = $order['cart_details']['extra_info']['billing']['phone'] ?? '5320000000';
        $tc_kimlik = (string)$order['cart_details']['extra_info']['billing_extra']['tc_no'] ?? '11111111110';

        //musteri parasutte kayitli mi?
        $parasut_customer = ParasutCustomer::where('name', $name)
            ->where('email', $email)
            ->where('phone', $phone)
            ->first();

        if ($parasut_customer) {
            $parasut_customer_id = $parasut_customer->parasut_id;
        } else {

            $create_parasut_customer = self::createCustomer($order['cart_details']['extra_info']['billing'], $order['cart_details']['extra_info']['billing_extra']);

            if (!$create_parasut_customer['success']) {

                //musteri olusturamazsak fatura surecine devam etmeyelim.

                return [
                    'success' => false,
                    'message' => 'Paraşüt müşteri oluşturulurken hata oluştu.'
                ];
            }

            $parasut_customer_id = $create_parasut_customer['parasut_customer_data']['id'];

            //db ye kaydet
            ParasutCustomer::create([
                'parasut_id' => $parasut_customer_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ]);
        }


        //simdi urunleri hazirlayalim.
        $urunler_arr = [];
        $urunler_error_count = 0;

        foreach ($order->products as $key => $order_product) {

            $quantity = $order_product->count;
            $birim_fiyat = $order_product->price / $quantity;
            $vergisiz_birim_fiyat = $birim_fiyat / (1 + 0.10);

            $parasut_product_id = null;

            $product = $order_product->product;

            if ($product->parasut) {
                $parasut_product_id = $product->parasut->parasut_id;
            } else {

                    $create_parasut_product = self::createProduct($product->id, $product->name);

                if ($create_parasut_product['success']) {

                    $parasut_product_id = $create_parasut_product['parasut_product_data']['id'];

                    $parasut_db = new ModelsParasut();
                    $parasut_db->parasut_id = $parasut_product_id;
                    $parasut_db->json_data = $create_parasut_product['parasut_product_data'];
                    $product->parasut()->save($parasut_db);

                } else {

                    $urunler_error_count++;
                }
            }

            if ($parasut_product_id) {

                $desc = [];

                if ($order_product->product_variant) {

                    $variant = $order_product->product_variant;

                    foreach ($variant['option_values'] as $option) {

                        if ($option['fe_visible'] && !empty($option['value'])) {
                            $desc[] = $option['value'];
                        }
                    }
                }

                $urunler_arr[] = [
                    'type' => 'sales_invoice_details',
                    'attributes' => array(
                        'quantity' => $quantity,
                        'unit_price' => $vergisiz_birim_fiyat,
                        'vat_rate' => 10,
                        'description' => implode(' - ', $desc) //buraya varsa variant detaylarini yazalim.
                    ),
                    "relationships" => array(
                        "product" => array(
                            "data" => array(
                                "id" => $parasut_product_id,
                                "type" => "products"
                            )
                        )
                    )
                ];
            }
        }

        //urunleri parasute eklerken hata ciktiyse yine devam etmeyelim.
        if( $urunler_error_count > 0 ){
            return [
                'success' => false,
                'message' => 'Paraşüt ürün oluşturulurken hata oluştu.'
            ];
        }
        

        $fatura = array(
            'data' => array(
                'type' => 'sales_invoices', // Required
                'attributes' => array(
                    'item_type' => 'invoice', // Required
                    'description' => '', //burayi bos birakalim genel aciklama
                    'issue_date' => $now, // Required
                    'currency' => 'TRL',
                    "payment_status" => "paid",
                    "billing_address" => $address,
                    "billing_phone" => '+90' . substr(str_replace(' ', '', $phone), -10),
                    "tax_number" => $tc_kimlik,
                ),
                'relationships' => array(
                    'details' => array(
                        'data' => $urunler_arr
                    ),
                    'contact' => array(
                        'data' => array(
                            'id' => $parasut_customer_id,
                            'type' => 'contacts'
                        )
                    )
                ),
            )
        );

        //indirim varsa onu ekleyelim.
        if ($order->subtotal > $order->total) {

            $indirim = $order->subtotal - $order->total;

            //indirimi brute uyguladigindan fatura tutari eksik kalmamasi icin indirim de brut olmali. yani vergi kadar az olmali.
            $vergisiz_indirim = $indirim / 1.1;

            $fatura['data']['attributes']['invoice_discount_type'] = 'amount';
            $fatura['data']['attributes']['invoice_discount'] = $vergisiz_indirim; 
        }

        try {

            $response = Http::withoutVerifying()
                ->withOptions(["verify" => false])
                ->withToken($token)
                ->post($this->cridentals()['api_url'] . '/sales_invoices', $fatura);

            $response_body = json_decode($response->body(), true);

            if ($response->successful()) {

                $order->parasut_fatura_id = $response_body['data']['id'];
                $order->save();

                $earsivgonder = self::aArsivFaturaGonder($order);

                return [
                    'success' => true,
                    'parasut_fatura_data' => $response_body['data']
                ];
            }

            Log::error('Parasut fatura olustururken hata dondu:');
            Log::error(print_r($response_body, true));

            return [
                'success' => false
            ];
        } catch (\Exception $e) {

            Log::error('Parasut fatura olustururken catch error:');
            Log::error($e);

            return [
                'success' => false
            ];
        }
    }



    public function aArsivFaturaGonder(Order $order)
    {

        $token = $this->getToken();

        $now = now();
        $today = \Carbon\Carbon::now()->format('Y-d-m');

        try {
            $invArr = array (
                "data" => array(
                    "type" => "e_archives",
                    "attributes" => array (
                        "internet_sale" => array (
                            "url" => config('app-ea.app_url'),
                            "payment_type" => 'KREDIKARTI/BANKAKARTI',
                            "payment_platform" => "iyzico",
                            "payment_date" => $today
                        )
                    ),
                    "relationships" => array (
                        "sales_invoice" => array (
                            "data" => array (
                                "id" => $order->parasut_fatura_id,
                                "type" => "sales_invoices"
                            )
                        )
                    )
                )
            );

            $response = Http::withoutVerifying()
                ->withOptions(["verify" => false])
                ->withToken($token)
                ->post($this->cridentals()['api_url'] . '/e_archives', $invArr);

            $response_body = json_decode($response->body(), true);

            if ($response->successful()) {

                $order->parasut_earsiv_id = $response_body['data']['id'];

                return [
                    'success' => true,
                    'parasut_earsiv_data' => $response_body['data']
                ];
            }

            Log::error('Parasut earsiv gonderirken olustururken hata dondu:');
            Log::error(print_r($response_body, true));

            return [
                'success' => false
            ];
        } catch (\Exception $e) {

            Log::error('Parasut  earsiv gonderirken olustururken catch error:');
            Log::error($e);

            return [
                'success' => false
            ];
        }
    }
}
