<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('fehome');
Route::post('/submit-form/{form}', [App\Http\Controllers\HomeController::class, 'formSubmit'])->middleware(ProtectAgainstSpam::class)->name('form.submit');
Route::middleware('doNotCacheResponse')->get('/b', [App\Http\Controllers\SearchController::class, 'blog'])->name('search.blog');
Route::middleware('doNotCacheResponse')->get('/s', [App\Http\Controllers\SearchController::class, 'content'])->name('search.content');
Route::middleware('doNotCacheResponse')->get('sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::middleware('doNotCacheResponse')->get('adclick/{commercial_ad}', [App\Http\Controllers\CommercialAdController::class, 'click'])->name('adclick');

foreach (config('languages.active') as $key => $lang) {
    if ($lang != config('languages.default')) {
        Route::get($lang, [App\Http\Controllers\HomeController::class, 'indexLang'])->name('fehome.lang');
    }
}

Route::middleware('doNotCacheResponse')->get('robots.txt', function() {


    $data = [
        'sitemaps' => [],
        'disallows' => [],
    ];

    if (app()->environment() == 'production') {
        
        $data = [
            'sitemaps' => [route('sitemap')],
            'disallows' => config('robots.disallows'),
        ];

    } else {
        $data = [
            'sitemaps' => [route('sitemap')],
            'disallows' => [
                '*'
            ],
        ];
    }

    return response(view('robots',[
        'data' => $data
    ]), 200, [
        'Content-Type' => 'text/plain'
    ]);
});

//Country, states, city
Route::get('/countries', App\Http\Controllers\CountryController::class)->name('countries');
Route::get('/countries/states/{country_id}', [App\Http\Controllers\CountryController::class, 'states'])->name('countries.states');
Route::get('/states/cities/{state_id}', [App\Http\Controllers\StateController::class, 'cities'])->name('states.cities');

Route::prefix('cart')->group(function () {
        
    Route::get('', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('add-item', [App\Http\Controllers\CartController::class, 'addItem'])->name('cart.add-item');
    Route::post('apply-coupon', [App\Http\Controllers\CartController::class, 'applyCouponCode'])->name('cart.apply-coupon');
    Route::post('remove-coupon', [App\Http\Controllers\CartController::class, 'removeCouponCode'])->name('cart.remove-coupon');
    Route::post('apply-gift-voucher', [App\Http\Controllers\CartController::class, 'applyGiftVoucher'])->name('cart.apply-gift-voucher');
    Route::post('remove-gift-voucher', [App\Http\Controllers\CartController::class, 'removeGiftVoucher'])->name('cart.remove-gift-voucher');
    Route::post('apply-campaign', [App\Http\Controllers\CartController::class, 'applyCampaign'])->name('cart.apply-campaign');
    Route::post('remove-campaign', [App\Http\Controllers\CartController::class, 'removeCampaign'])->name('cart.remove-campaign');
    Route::put('update-item/{hash}', [App\Http\Controllers\CartController::class, 'updateItem'])->name('cart.update-item');
    Route::delete('destroy', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('remove/{item_hash}', [App\Http\Controllers\CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('change-quantity/{item_hash}/{quantity}', [App\Http\Controllers\CartController::class, 'changeItemQuantity'])->name('cart.change.quantity');

});

Route::prefix('payment')->middleware('doNotCacheResponse')->group(function () {
            
    Route::get('', [App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index')->middleware(['checkCartHasItems']);
    Route::get('/guest', [App\Http\Controllers\PaymentController::class, 'guest'])->name('payment.guest')->middleware(['checkCartHasItems']);
    Route::post('/pay', [App\Http\Controllers\PaymentController::class, 'payStart'])->name('payment.pay')->middleware(ProtectAgainstSpam::class)->middleware(['checkCartHasItems']);
    Route::post('/pay/iyzico-callback', [App\Http\Controllers\PaymentController::class, 'iyzicoCallback'])->name('payment.pay.iyzico.callback');
    Route::get('/pay/success/{order:code}', [App\Http\Controllers\PaymentController::class, 'orderSuccess'])->name('payment.pay.success');

});


//newsletter
Route::resource('newsletter-members', App\Http\Controllers\NewsletterMemberController::class);

//appointment
Route::post('/appointment/submit', [App\Http\Controllers\AppointmentController::class, 'store'])->middleware(ProtectAgainstSpam::class)->name('appointment-form.submit');

//Ajax
Route::prefix('fetch')->group(function () {
        
    Route::get('product-details/{product:uuid}', [App\Http\Controllers\FetchController::class, 'productDetails'])->name('ajax.product.details');
    Route::get('variant-selection/{product:uuid}', [App\Http\Controllers\FetchController::class, 'variantSelection'])->name('ajax.variant.selection');
    Route::get('product-option-selection/{product:uuid}', [App\Http\Controllers\FetchController::class, 'productOptionSelection'])->name('ajax.product.option.selection');
    Route::get('variant-selection-buttons/{productVariant}', [App\Http\Controllers\FetchController::class, 'variantPrepareAddToCartButton'])->name('ajax.variant.selection.buttons');

    Route::get('offcanvas-content/{uuid}/{language}', [App\Http\Controllers\HomeController::class, 'fetchOffcanvasContent'])->name('fetch.offcanvas.content');
    
    Route::get('working-hours/get-available-slots', [App\Http\Controllers\FetchController::class, 'getAvailableWorkingHourSlots'])->name('fetch.working-hours.slots');

});

Route::middleware(['auth'])->prefix('hesabim')->group(function(){
    Route::get('', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/hesap-bilgilerim', [ProfileController::class, 'account'])->name('profile.account');
    Route::patch('/hesap-bilgilerim', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/siparislerim', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::resource('adres', App\Http\Controllers\UserAddressController::class);
});

//kalan hersey tek yerde.
Route::fallback([App\Http\Controllers\HomeController::class, 'handleFe']);