<?php

namespace App\Http\Libraries;

use Jackiedo\Cart\Cart;
use Illuminate\Support\Str;
use App\Settings\GlobalSettings;
use Illuminate\Support\Facades\Log;

class IyzicoLibrary
{
    public static function generateScript(Cart $cart,$sessionId)
    {

        $settings = app(GlobalSettings::class);
        
        $options = new \Iyzipay\Options();
        $options->setApiKey(config('app-ea.iyzico_key'));
        $options->setSecretKey(config('app-ea.iyzico_secret'));
        $options->setBaseUrl(config('app-ea.iyzico_url'));

        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(app()->getLocale());
        $request->setConversationId(Str::uuid());
        $request->setPrice($cart->getTotal());
        $request->setPaidPrice($cart->getTotal());
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($sessionId);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl(route('payment.pay.iyzico.callback',['sid' => $sessionId]));
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(Str::uuid());
        $buyer->setName($cart->getDetails()['extra_info']['billing']['name']);
        $buyer->setSurname($cart->getDetails()['extra_info']['billing']['name']);
        $buyer->setGsmNumber($cart->getDetails()['extra_info']['billing']['phone']);
        $buyer->setEmail($cart->getDetails()['extra_info']['billing']['email']);
        $buyer->setIdentityNumber("000000000");
        $buyer->setLastLoginDate(date("Y-m-d H:i:s"));
        $buyer->setRegistrationDate(date("Y-m-d H:i:s"));
        $buyer->setRegistrationAddress($cart->getDetails()['extra_info']['billing']['address']);
        //$buyer->setIp("0.0.0.0");
        $buyer->setCity($cart->getDetails()['extra_info']['billing']['state']);
        $buyer->setCountry("Turkiye");
        $buyer->setZipCode("34000");
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($cart->getDetails()['extra_info']['shipping']['name']);
        $shippingAddress->setCity($cart->getDetails()['extra_info']['shipping']['state']);
        $shippingAddress->setCountry("Turkiye");
        $shippingAddress->setAddress($cart->getDetails()['extra_info']['shipping']['address']);
        $shippingAddress->setZipCode("34000");
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($cart->getDetails()['extra_info']['billing']['name']);
        $billingAddress->setCity($cart->getDetails()['extra_info']['billing']['state']);
        $billingAddress->setCountry("Turkiye");
        $billingAddress->setAddress($cart->getDetails()['extra_info']['billing']['address']);
        $billingAddress->setZipCode("34000");
        $request->setBillingAddress($billingAddress);

        $basketItems = [];

        //indirimlerde vs item toplami ile sepet toplami esit olmadigindan iyzico hata veriyor. direkt 1 item ile toplam sepet tutarini gonderelim.
        // foreach ($cart->getItems() as $key => $item) {
            
        //     $firstBasketItem = new \Iyzipay\Model\BasketItem();
        //     $firstBasketItem->setId($item->getId());
        //     $firstBasketItem->setName($item->getTitle());
        //     $firstBasketItem->setCategory1(config('app-ea.app_name));
        //     $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        //     $firstBasketItem->setPrice($item->getSubtotal());
        //     $basketItems[] = $firstBasketItem;

        // }

        // if($cart->getTotal() > $cart->getItemsSubtotal()){
        //     $otherItems = new \Iyzipay\Model\BasketItem();
        //     $otherItems->setId("OTHERS");
        //     $otherItems->setName("Diğer Ücretler");
        //     $otherItems->setCategory1(config('app-ea.app_name));
        //     $otherItems->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        //     $otherItems->setPrice($cart->getTotal() - $cart->getItemsSubtotal());
        //     $basketItems[] = $otherItems;
        // }

        $basketItemOne = new \Iyzipay\Model\BasketItem();
        $basketItemOne->setId(Str::uuid());
        $basketItemOne->setName(config('app-ea.app_name'));
        $basketItemOne->setCategory1(config('app-ea.app_name'));
        $basketItemOne->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $basketItemOne->setPrice($cart->getTotal());
        $basketItems[] = $basketItemOne;

        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);

        $checkOutFormStatus = $checkoutFormInitialize->getStatus();

        if ($checkOutFormStatus == 'success') {

            $script = $checkoutFormInitialize->getCheckoutFormContent();
            //$token = $checkoutFormInitialize->getToken();

            return [
                'status' => 'success',
                'script' => $script
            ];

        } else {

            //print_r($checkoutFormInitialize);

            Log::debug(print_r($checkoutFormInitialize,true));
            return [
                'status' => 'error',
                'message' => $checkoutFormInitialize->getErrorMessage()
            ];
        }
    }

    public static function checkPaymentStatus($token)
    {

        $settings = app(GlobalSettings::class);

        $options = new \Iyzipay\Options();
        $options->setApiKey(config('app-ea.iyzico_key'));
        $options->setSecretKey(config('app-ea.iyzico_secret'));
        $options->setBaseUrl(config('app-ea.iyzico_url'));

        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId(Str::uuid());
        $request->setToken($token);

        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $options);

        $checkoutStatus = $checkoutForm->getPaymentStatus();

        if ($checkoutStatus == 'SUCCESS') {

            return [
                'status' => 'success'
            ];

        } else {

            Log::debug('Odeme hatasi:'.$token.' Status:'.$checkoutStatus.' Aciklama'.$checkoutForm->getErrorMessage());

            return [
                'status' => 'error',
                'message' => $checkoutForm->getErrorMessage()
            ];

        }
    }

}
