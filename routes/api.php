<?php

use App\Http\Controllers\BanktransactionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExchangerateController;
use App\Http\Controllers\InventoryitemController;
use App\Http\Controllers\invoiceController;
use App\Http\Controllers\PublicWorkshopController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





Route::post("sendPayment", [BanktransactionController::class, "create"])->name("sendPayment");
Route::get("recallPayment/{referencenumber}", [BanktransactionController::class, "recallpayment"])->name("recallPayment");
Route::post("BankTransaction/Search", [BanktransactionController::class, "search"])->name("banktransaction.search");
Route::post("BankTransaction/Claims", [BanktransactionController::class, "claim"])->name("banktransaction.claim");
Route::get("account", [CustomerController::class, "index"]);
Route::get("account/getbyregnumber/{regnumber}", [CustomerController::class, "getbyregnumber"]);
Route::post("account/Verification", [CustomerController::class, "verifycustomer"]);
Route::post("account", [CustomerController::class, "createcustomer"]);
Route::put("account", [CustomerController::class, "updatecustomer"]);
Route::get("InventoryItem", [InventoryitemController::class, "getinventories"]);
Route::get("Invoice/{invoicenumber}", [invoiceController::class, "show"]);
Route::post("Invoice/Create", [invoiceController::class, "store"]);
Route::get("ExchangeRate/GetLatest/{currency_id?}", [ExchangerateController::class, "getlatest"]);
Route::delete("Invoice/{invoicenumber}", [invoiceController::class, "destroy"]);
Route::post("Wallet", [WalletController::class, "getwalletbalance"]);
Route::get("Wallet/{regnumber}", [WalletController::class, "getwallet"]);


// Public Workshop API Routes
Route::prefix('public-workshops')->group(function () {
    // Workshop information
    Route::get('/', [PublicWorkshopController::class, 'getPublishedWorkshops'])->name('public-workshop.list');
    Route::get('/{id}/preview-document', [PublicWorkshopController::class, 'previewDocument'])->name('public-workshop.preview-document');

    // Customer search
    Route::post('/search-customer', [PublicWorkshopController::class, 'searchCustomer'])->name('public-workshop.search-customer');

    // Workshop orders - put these BEFORE the generic /{id} route
    Route::get('/{workshopId}/orders/{customerId}', [PublicWorkshopController::class, 'getWorkshopOrder'])->name('public-workshop.get-order');
    Route::post('/orders', [PublicWorkshopController::class, 'createOrder'])->name('public-workshop.create-order');
    Route::post('/public-orders', [PublicWorkshopController::class, 'createPublicOrder'])->name('public-workshop.create-public-order');
    Route::get('/orders/{orderId}/delegate-info', [PublicWorkshopController::class, 'getOrderWithDelegateCount'])->name('public-workshop.order-delegate-info');
    Route::post('/orders/{orderId}/payment', [PublicWorkshopController::class, 'savePayment'])->name('public-workshop.save-payment');
    Route::get('/orders/{orderId}/download', [PublicWorkshopController::class, 'downloadOrder'])->name('public-workshop.download-order');

    // Exchange rates
    Route::post('/exchange-rates', [PublicWorkshopController::class, 'getExchangeRates'])->name('public-workshop.exchange-rates');

    // Delegates
    Route::get('/orders/{orderId}/delegates', [PublicWorkshopController::class, 'getDelegates'])->name('public-workshop.get-delegates');
    Route::post('/delegates', [PublicWorkshopController::class, 'createDelegate'])->name('public-workshop.create-delegate');
    Route::put('/delegates/{delegateId}', [PublicWorkshopController::class, 'updateDelegate'])->name('public-workshop.update-delegate');
    Route::delete('/delegates/{delegateId}', [PublicWorkshopController::class, 'deleteDelegate'])->name('public-workshop.delete-delegate');

    // Utility
    Route::post('/calculate-amount', [PublicWorkshopController::class, 'calculateAmount'])->name('public-workshop.calculate-amount');

    // Put the generic /{id} route LAST
    Route::get('/{id}', [PublicWorkshopController::class, 'getWorkshop'])->name('public-workshop.show');
});
