<?php

use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth','isAdmin','doNotCacheResponse'])->group(function () {
    Route::get('', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard');


    Route::get('my-profile', [App\Http\Controllers\AdminProfileController::class, 'index'])->name('my.index');
    Route::put('my-profile', [App\Http\Controllers\AdminProfileController::class, 'update']);
    Route::post('my-profile/change-password', [App\Http\Controllers\AdminProfileController::class, 'updatePassword'])->name('my.updatepassword');

    //Roles
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    //Permission Groups
    Route::resource('permission-groups', App\Http\Controllers\PermissionGroupController::class);

    //users
    Route::get('users/trash', [App\Http\Controllers\UserController::class, 'trash'])->name('users.trash');
    Route::delete('users/bulk/delete', [App\Http\Controllers\UserController::class, 'bulkDelete'])->name('users.bulk.delete');
    Route::delete('users/bulk/destroy', [App\Http\Controllers\UserController::class, 'bulkDestroy'])->name('users.bulk.destroy');
    Route::put('users/bulk/restore', [App\Http\Controllers\UserController::class, 'bulkRestore'])->name('users.bulk.restore');
    Route::resource('users', App\Http\Controllers\UserController::class)->withTrashed(['destroy']);
    Route::delete('users/{user}/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
    Route::put('users/{user}/restore', [App\Http\Controllers\UserController::class, 'restore'])->withTrashed()->name('users.restore');
    Route::post('users/{user}/avatar', [App\Http\Controllers\UserController::class, 'avatar'])->name('users.avatar');

    //customers
    Route::get('customers', [App\Http\Controllers\UserController::class, 'customers'])->name('customers.index');
    Route::delete('customers/bulk/delete', [App\Http\Controllers\UserController::class, 'customersBulkDelete'])->name('customers.bulk.delete');
    Route::delete('customers/bulk/destroy', [App\Http\Controllers\UserController::class, 'customersBulkDestroy'])->name('customers.bulk.destroy');
    Route::put('customers/bulk/restore', [App\Http\Controllers\UserController::class, 'customersBulkRestore'])->name('customers.bulk.restore');
    Route::get('customers/trash', [App\Http\Controllers\UserController::class, 'customersTrash'])->name('customers.trash');
    Route::get('customers/{customer}', [App\Http\Controllers\UserController::class, 'customerShow'])->name('customers.show');
    Route::delete('customers/{customer}/delete', [App\Http\Controllers\UserController::class, 'customersDelete'])->name('customers.delete');
    Route::delete('customers/{customer}/destroy', [App\Http\Controllers\UserController::class, 'customersDestroy'])->withTrashed()->name('customers.destroy');
    Route::put('customers/{customer}/restore', [App\Http\Controllers\UserController::class, 'customersRestore'])->withTrashed()->name('customers.restore');

    //media
    Route::resource('medias', App\Http\Controllers\MediaLibraryController::class);
    Route::delete('medias/bulk/delete', [App\Http\Controllers\MediaLibraryController::class, 'bulkDelete'])->name('medias.bulk.delete');

    //Content Type
    Route::resource('content-types', App\Http\Controllers\ContentTypeController::class);
    Route::delete('content-types/{content_type}/delete', [App\Http\Controllers\ContentTypeController::class, 'delete'])->name('content-types.delete');
    Route::post('content-types/{content_type}/restore', [App\Http\Controllers\ContentTypeController::class, 'restore'])->name('content-types.restore');

    //Content Category
    Route::get('contents-categories/reorder', [App\Http\Controllers\ContentCategoryController::class, 'getReorder'])->name('content-categories.reorder');
    Route::post('contents-categories/reorder', [App\Http\Controllers\ContentCategoryController::class, 'reorder']);
    Route::get('contents-categories/trash', [App\Http\Controllers\ContentCategoryController::class, 'trash'])->name('content-categories.trash');
    Route::delete('content-categories/bulk/delete', [App\Http\Controllers\ContentCategoryController::class, 'bulkDelete'])->name('content-categories.bulk.delete');
    Route::delete('content-categories/bulk/destroy', [App\Http\Controllers\ContentCategoryController::class, 'bulkDestroy'])->name('content-categories.bulk.destroy');
    Route::put('content-categories/bulk/restore', [App\Http\Controllers\ContentCategoryController::class, 'bulkRestore'])->name('content-categories.bulk.restore');
    Route::resource('content-categories', App\Http\Controllers\ContentCategoryController::class)->withTrashed(['destroy']);
    Route::delete('content-categories/{content_category}/delete', [App\Http\Controllers\ContentCategoryController::class, 'delete'])->name('content-categories.delete');
    Route::put('content-categories/{content_category}/restore', [App\Http\Controllers\ContentCategoryController::class, 'restore'])->withTrashed()->name('content-categories.restore');

    //Content
    Route::get('contents/reorder', [App\Http\Controllers\ContentController::class, 'getReorder'])->name('contents.reorder');
    Route::post('contents/reorder', [App\Http\Controllers\ContentController::class, 'reorder']);
    Route::get('contents/trash', [App\Http\Controllers\ContentController::class, 'trash'])->name('contents.trash');
    Route::delete('contents/bulk/delete', [App\Http\Controllers\ContentController::class, 'bulkDelete'])->name('contents.bulk.delete');
    Route::delete('contents/bulk/destroy', [App\Http\Controllers\ContentController::class, 'bulkDestroy'])->name('contents.bulk.destroy');
    Route::put('contents/bulk/restore', [App\Http\Controllers\ContentController::class, 'bulkRestore'])->name('contents.bulk.restore');
    Route::resource('contents', App\Http\Controllers\ContentController::class)->withTrashed(['destroy']);
    Route::delete('contents/{content}/delete', [App\Http\Controllers\ContentController::class, 'delete'])->name('contents.delete');
    Route::put('contents/{content}/restore', [App\Http\Controllers\ContentController::class, 'restore'])->withTrashed()->name('contents.restore');

    //Content Preview
    Route::resource('content-previews', App\Http\Controllers\ContentPreviewController::class)->withTrashed(['destroy']);

    //Content Attributes
    Route::resource('content-attributes', App\Http\Controllers\ContentAttributeController::class);
    Route::delete('content-attributes/{content_attribute}/delete', [App\Http\Controllers\ContentAttributeController::class, 'delete'])->name('content-attributes.delete');
    Route::post('content-attributes/{content_attribute}/restore', [App\Http\Controllers\ContentAttributeController::class, 'restore'])->name('content-attributes.restore');

    //Sliders
    Route::resource('sliders', App\Http\Controllers\SliderController::class);
    Route::delete('sliders/{slider}/delete', [App\Http\Controllers\SliderController::class, 'delete'])->name('sliders.delete');
    Route::post('sliders/{slider}/restore', [App\Http\Controllers\SliderController::class, 'restore'])->name('sliders.restore');

    //Slider Slides
    Route::resource('slider-slides', App\Http\Controllers\SliderSlideController::class);
    Route::post('slider-slides/reorder', [App\Http\Controllers\SliderSlideController::class, 'reorder'])->name('slider-slides.reorder');
    Route::delete('slider-slides/{slider_slide}/delete', [App\Http\Controllers\SliderSlideController::class, 'delete'])->name('slider-slides.delete');
    Route::post('slider-slides/{slider_slide}/restore', [App\Http\Controllers\SliderSlideController::class, 'restore'])->name('slider-slides.restore');

    //Menus
    Route::get('menus/get-linkable-contents', [App\Http\Controllers\MenuController::class, 'linkableContents'])->name('menus.contents');
    Route::resource('menus', App\Http\Controllers\MenuController::class);

    //Forms
    Route::resource('forms', App\Http\Controllers\FormController::class);
    Route::delete('forms/{form}/delete', [App\Http\Controllers\FormController::class, 'delete'])->name('forms.delete');
    Route::post('forms/{form}/restore', [App\Http\Controllers\FormController::class, 'restore'])->name('forms.restore');

    //FormEntries
    Route::resource('form-entries', App\Http\Controllers\FormEntryController::class);
    
    //Design Templates
    Route::resource('design-templates', App\Http\Controllers\DesignTemplateController::class);
    Route::delete('design-templates/{design_template}/delete', [App\Http\Controllers\DesignTemplateController::class, 'delete'])->name('design-templates.delete');
    Route::post('design-templates/{design_template}/restore', [App\Http\Controllers\DesignTemplateController::class, 'restore'])->name('design-templates.restore');

    //Sidebars
    Route::resource('sidebars', App\Http\Controllers\SidebarController::class);
    Route::delete('sidebars/{sidebar}/delete', [App\Http\Controllers\SidebarController::class, 'delete'])->name('sidebars.delete');
    Route::post('sidebars/{sidebar}/restore', [App\Http\Controllers\SidebarController::class, 'restore'])->name('sidebars.restore');

    //Layouts
    Route::resource('layouts', App\Http\Controllers\LayoutController::class);
    Route::delete('layouts/{layout}/delete', [App\Http\Controllers\LayoutController::class, 'delete'])->name('layouts.delete');
    Route::post('layouts/{layout}/restore', [App\Http\Controllers\LayoutController::class, 'restore'])->name('layouts.restore');

    //Card Layouts
    Route::resource('card-layouts', App\Http\Controllers\CardLayoutController::class);
    Route::delete('card-layouts/{card_layout}/delete', [App\Http\Controllers\CardLayoutController::class, 'delete'])->name('card-layouts.delete');
    Route::post('card-layouts/{card_layout}/restore', [App\Http\Controllers\CardLayoutController::class, 'restore'])->name('card-layouts.restore');

    //Header Layouts
    Route::resource('header-layouts', App\Http\Controllers\HeaderLayoutController::class);
    Route::delete('header-layouts/{header_layout}/delete', [App\Http\Controllers\HeaderLayoutController::class, 'delete'])->name('header-layouts.delete');
    Route::post('header-layouts/{header_layout}/restore', [App\Http\Controllers\HeaderLayoutController::class, 'restore'])->name('header-layouts.restore');

    //TAGS
    Route::resource('tags', App\Http\Controllers\TagController::class);
    Route::delete('tags/bulk/delete', [App\Http\Controllers\TagController::class, 'bulkDelete'])->name('tags.bulk.delete');
    Route::delete('tags/{tag}/delete', [App\Http\Controllers\TagController::class, 'delete'])->name('tags.delete');
    Route::post('tags/{tag}/restore', [App\Http\Controllers\TagController::class, 'restore'])->name('tags.restore');
    
    //Email contents
    Route::resource('email-contents', App\Http\Controllers\EmailContentController::class);

    //Settings
    Route::prefix('settings')->group(function(){
        Route::get('', [App\Http\Controllers\GlobalSettingsController::class, 'index'])->name('settings.index');
        Route::put('', [App\Http\Controllers\GlobalSettingsController::class, 'update'])->name('settings.update');
    });

    //URL
    Route::resource('links', App\Http\Controllers\LinkController::class);

    //Saved elements
    Route::resource('saved-sections', App\Http\Controllers\SavedSectionController::class);
    Route::resource('saved-containers', App\Http\Controllers\SavedContainerController::class);
    Route::resource('saved-rows', App\Http\Controllers\SavedRowController::class);
    Route::resource('saved-columns', App\Http\Controllers\SavedColumnController::class);
    Route::resource('saved-elements', App\Http\Controllers\SavedElementController::class);

    //Announcements
    Route::resource('announcements', App\Http\Controllers\AnnouncementController::class);
    Route::delete('announcements/{announcement}/delete', [App\Http\Controllers\AnnouncementController::class, 'delete'])->name('announcements.delete');
    Route::post('announcements/{announcement}/restore', [App\Http\Controllers\AnnouncementController::class, 'restore'])->name('announcements.restore');


    //Redirect Uris
    Route::resource('redirect-uris', App\Http\Controllers\RedirectUriController::class);

    //Atisan
    Route::get('artisan-command', [App\Http\Controllers\CommandController::class, 'index'])->name('artisan.command');
    Route::post('artisan-command', [App\Http\Controllers\CommandController::class, 'execute']);

    //Working Hours
    Route::resource('working-hours', App\Http\Controllers\WorkingHourController::class);
    //Appointments
    Route::resource('appointments', App\Http\Controllers\AppointmentController::class);
    Route::delete('announcements/{appointment}/delete', [App\Http\Controllers\AppointmentController::class, 'delete'])->name('appointments.delete');
    Route::post('announcements/{appointment}/restore', [App\Http\Controllers\AppointmentController::class, 'restore'])->name('appointments.restore');
    

    //FETCH
    Route::prefix('fetch')->group(function(){
        Route::get('', [App\Http\Controllers\FetchController::class, 'contentCategories'])->name('fetch.content-categories');
        Route::get('links', [App\Http\Controllers\FetchController::class, 'links'])->name('fetch.links');
    });

    //ADS AREA
    Route::get('commercial-ad-areas/trash', [App\Http\Controllers\CommercialAdAreaController::class, 'trash'])->name('commercial-ad-areas.trash');
    Route::resource('commercial-ad-areas', App\Http\Controllers\CommercialAdAreaController::class)->withTrashed(['destroy']);
    Route::delete('commercial-ad-areas/{commercial_ad_area}/delete', [App\Http\Controllers\CommercialAdAreaController::class, 'delete'])->name('commercial-ad-areas.delete');
    Route::put('commercial-ad-areas/{commercial_ad_area}/restore', [App\Http\Controllers\CommercialAdAreaController::class, 'restore'])->withTrashed()->name('commercial-ad-areas.restore');

    //ADS
    Route::get('commercial-ads/reorder', [App\Http\Controllers\CommercialAdController::class, 'getReorder'])->name('commercial-ads.reorder');
    Route::post('commercial-ads/reorder', [App\Http\Controllers\CommercialAdController::class, 'reorder']);
    Route::get('commercial-ads/trash', [App\Http\Controllers\CommercialAdController::class, 'trash'])->name('commercial-ads.trash');
    Route::delete('commercial-ads/bulk/delete', [App\Http\Controllers\CommercialAdController::class, 'bulkDelete'])->name('commercial-ads.bulk.delete');
    Route::delete('commercial-ads/bulk/destroy', [App\Http\Controllers\CommercialAdController::class, 'bulkDestroy'])->name('commercial-ads.bulk.destroy');
    Route::put('commercial-ads/bulk/restore', [App\Http\Controllers\CommercialAdController::class, 'bulkRestore'])->name('commercial-ads.bulk.restore');
    Route::resource('commercial-ads', App\Http\Controllers\CommercialAdController::class)->withTrashed(['destroy']);
    Route::delete('commercial-ads/{commercial_ad}/delete', [App\Http\Controllers\CommercialAdController::class, 'delete'])->name('commercial-ads.delete');
    Route::put('commercial-ads/{commercial_ad}/restore', [App\Http\Controllers\CommercialAdController::class, 'restore'])->withTrashed()->name('commercial-ads.restore');
    Route::put('commercial-ads/{commercial_ad}/publish', [App\Http\Controllers\CommercialAdController::class, 'publish'])->withTrashed()->name('commercial-ads.publish');
    Route::put('commercial-ads/{commercial_ad}/unpublish', [App\Http\Controllers\CommercialAdController::class, 'unpublish'])->withTrashed()->name('commercial-ads.unpublish');


    //Product Categories
    Route::resource('product-categories', App\Http\Controllers\ProductCategoryController::class);
    Route::delete('product-categories/bulk/delete', [App\Http\Controllers\ProductCategoryController::class, 'bulkDelete'])->name('product-categories.bulk.delete');
    Route::delete('product-categories/{product_category}/delete', [App\Http\Controllers\ProductCategoryController::class, 'delete'])->name('product-categories.delete');
    Route::post('product-categories/{product_category}/restore', [App\Http\Controllers\ProductCategoryController::class, 'restore'])->name('product-categories.restore');

    //Product Types
    Route::resource('product-types', App\Http\Controllers\ProductTypeController::class);
    Route::delete('product-types/{product_type}/delete', [App\Http\Controllers\ProductTypeController::class, 'delete'])->name('product-types.delete');
    Route::post('product-types/{product_type}/restore', [App\Http\Controllers\ProductTypeController::class, 'restore'])->name('product-types.restore');
    Route::get('product-types/{product_type}/sales', [App\Http\Controllers\ProductTypeController::class, 'sales'])->name('product-types.sales');

    //Poduct Option Groups
    Route::resource('product-option-groups', App\Http\Controllers\ProductOptionGroupController::class);
    Route::delete('product-option-groups/{product_option_group}/delete', [App\Http\Controllers\ProductOptionGroupController::class, 'delete'])->name('product-option-groups.delete');
    Route::post('product-option-groups/{product_option_group}/restore', [App\Http\Controllers\ProductOptionGroupController::class, 'restore'])->name('product-option-groups.restore');

    //Poduct Option Group Options
    Route::resource('product-option-group-options', App\Http\Controllers\ProductOptionGroupOptionController::class);
    Route::post('product-option-group-options/reorder', [App\Http\Controllers\ProductOptionGroupOptionController::class, 'reorder'])->name('product-option-group-options.reorder');
    Route::delete('product-option-group-options/{product_option_group_option}/delete', [App\Http\Controllers\ProductOptionGroupOptionController::class, 'delete'])->name('product-option-group-options.delete');
    Route::post('product-option-group-options/{product_option_group_option}/restore', [App\Http\Controllers\ProductOptionGroupOptionController::class, 'restore'])->name('product-option-group-options.restore');

    //Poduct Attributes
    Route::resource('product-attributes', App\Http\Controllers\ProductAttributeController::class);
    Route::delete('product-attributes/{product_attribute}/delete', [App\Http\Controllers\ProductAttributeController::class, 'delete'])->name('product-attributes.delete');
    Route::post('product-attributes/{product_attribute}/restore', [App\Http\Controllers\ProductAttributeController::class, 'restore'])->name('product-attributes.restore');

    //Poduct Customer Attributes
    Route::resource('product-customer-attributes', App\Http\Controllers\ProductCustomerAttributeController::class);
    Route::delete('product-customer-attributes/{product_customer_attribute}/delete', [App\Http\Controllers\ProductCustomerAttributeController::class, 'delete'])->name('product-customer-attributes.delete');
    Route::post('product-customer-attributes/{product_customer_attribute}/restore', [App\Http\Controllers\ProductCustomerAttributeController::class, 'restore'])->name('product-customer-attributes.restore');

    //Products
    Route::get('products/reorder', [App\Http\Controllers\ProductController::class, 'getReorder'])->name('products.reorder');
    Route::post('products/reorder', [App\Http\Controllers\ProductController::class, 'reorder']);
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::delete('products/bulk/delete', [App\Http\Controllers\ProductController::class, 'bulkDelete'])->name('products.bulk.delete');
    Route::delete('products/{product}/delete', [App\Http\Controllers\ProductController::class, 'delete'])->name('products.delete');
    Route::post('products/{product}/restore', [App\Http\Controllers\ProductController::class, 'restore'])->name('products.restore');
    //product_variants
    Route::get('products/{product}/variants', [App\Http\Controllers\ProductVariantController::class, 'index'])->name('product-variants.index');
    Route::post('products/{product}/variants', [App\Http\Controllers\ProductVariantController::class, 'store'])->name('product-variants.store');
    Route::put('products/{product}/variants/{product_variant}', [App\Http\Controllers\ProductVariantController::class, 'update'])->name('product-variants.update');
    Route::delete('products/{product}/variants/{product_variant}', [App\Http\Controllers\ProductVariantController::class, 'delete'])->name('product-variants.delete');
    Route::get('products/{product}/reorder-variants', [App\Http\Controllers\ProductVariantController::class, 'getReorder'])->name('product-variants.reorder');
    Route::post('products/{product}/reorder-variants', [App\Http\Controllers\ProductVariantController::class, 'reorder']);

    //Orders
    Route::resource('orders', App\Http\Controllers\OrderController::class);
    Route::put('orders/{order}/update-status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    //CouponGroups
    Route::resource('coupon-groups', App\Http\Controllers\CouponGroupController::class);
    Route::delete('coupon-groups/{coupon_group}/delete', [App\Http\Controllers\CouponGroupController::class, 'delete'])->name('coupon-groups.delete');
    Route::post('coupon-groups/{coupon_group}/restore', [App\Http\Controllers\CouponGroupController::class, 'restore'])->name('coupon-groups.restore');

    //Coupons
    Route::resource('coupons', App\Http\Controllers\CouponController::class);
    Route::delete('coupons/{coupon}/delete', [App\Http\Controllers\CouponController::class, 'delete'])->name('coupons.delete');
    Route::post('coupons/{coupon}/restore', [App\Http\Controllers\CouponController::class, 'restore'])->name('coupons.restore');
    Route::get('coupon-generate', [App\Http\Controllers\CouponController::class, 'generate'])->name('coupons.generate');

    //CARGO
    Route::resource('cargos', App\Http\Controllers\CargoController::class)->withTrashed(['destroy']);
    Route::delete('cargos/{cargo}/delete', [App\Http\Controllers\CargoController::class, 'delete'])->name('cargos.delete');
    Route::put('cargos/{cargo}/restore', [App\Http\Controllers\CargoController::class, 'restore'])->withTrashed()->name('cargos.restore');

    //TAXES
    Route::resource('taxes', App\Http\Controllers\TaxController::class)->withTrashed(['destroy']);
    Route::delete('taxes/{tax}/delete', [App\Http\Controllers\TaxController::class, 'delete'])->name('taxes.delete');
    Route::put('taxes/{tax}/restore', [App\Http\Controllers\TaxController::class, 'restore'])->withTrashed()->name('taxes.restore');

    //Bank Accounts
    Route::resource('bank-accounts', App\Http\Controllers\BankAccountController::class)->withTrashed(['destroy']);
    Route::delete('bank-accounts/{bank_account}/delete', [App\Http\Controllers\BankAccountController::class, 'delete'])->name('bank-accounts.delete');
    Route::put('bank-accounts/{bank_account}/restore', [App\Http\Controllers\BankAccountController::class, 'restore'])->withTrashed()->name('bank-accounts.restore');


    //Campaigns
    Route::get('campaigns/trash', [App\Http\Controllers\CampaignController::class, 'trash'])->name('campaigns.trash');
    Route::delete('campaigns/bulk/delete', [App\Http\Controllers\CampaignController::class, 'bulkDelete'])->name('campaigns.bulk.delete');
    Route::delete('campaigns/bulk/destroy', [App\Http\Controllers\CampaignController::class, 'bulkDestroy'])->name('campaigns.bulk.destroy');
    Route::put('campaigns/bulk/restore', [App\Http\Controllers\CampaignController::class, 'bulkRestore'])->name('campaigns.bulk.restore');
    Route::resource('campaigns', App\Http\Controllers\CampaignController::class)->withTrashed(['destroy']);
    Route::delete('campaigns/{campaign}/delete', [App\Http\Controllers\CampaignController::class, 'delete'])->name('campaigns.delete');
    Route::put('campaigns/{campaign}/restore', [App\Http\Controllers\CampaignController::class, 'restore'])->withTrashed()->name('campaigns.restore');
    
    //OLD IMPORT
    Route::prefix('old-import')->group(function(){
        //Route::get('users', [App\Http\Controllers\OldImportController::class, 'users']);
        //Route::get('users-new', [App\Http\Controllers\OldImportController::class, 'users_new']);
    });

});