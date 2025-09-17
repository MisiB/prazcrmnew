<?php

use App\Http\Controllers\BanktransactionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExchangerateController;
use App\Http\Controllers\InventoryitemController;
use App\Http\Controllers\invoiceController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





Route::post("sendPayment",[BanktransactionController::class,"create"])->name("sendPayment");
 Route::get("recallPayment/{referencenumber}",[BanktransactionController::class,"recallpayment"])->name("recallPayment");
 Route::post("BankTransaction/Search",[BanktransactionController::class,"search"])->name("banktransaction.search");
 Route::post("BankTransaction/Claims",[BanktransactionController::class,"claim"])->name("banktransaction.claim");
 Route::get("account",[CustomerController::class,"index"]);
 Route::get("account/getbyregnumber/{regnumber}",[CustomerController::class,"getbyregnumber"]);
 Route::post("account/Verification",[CustomerController::class,"verifycustomer"]);
 Route::post("account",[CustomerController::class,"createcustomer"]);
 Route::put("account",[CustomerController::class,"updatecustomer"]);
 Route::get("InventoryItem",[InventoryitemController::class,"getinventories"]);
 Route::get("Invoice/{invoicenumber}",[invoiceController::class,"show"]);
 Route::post("Invoice/Create",[invoiceController::class,"store"]);
 Route::get("ExchangeRate/GetLatest/{currency_id?}",[ExchangerateController::class,"getlatest"]);
 Route::delete("Invoice/{invoicenumber}",[invoiceController::class,"destroy"]);
 Route::post("Wallet",[WalletController::class,"getwalletbalance"]);
 Route::get("Wallet/{regnumber}",[WalletController::class,"getwallet"]);

