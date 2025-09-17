<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Interfaces\services\iinvoiceService;
use Illuminate\Http\Request;

class invoiceController extends Controller
{
    protected $service;
    public function __construct(iinvoiceService $service)
    {
        $this->service = $service;
    }

    public function show($invoice_number){
        return $this->service->getinvoice($invoice_number);
    }

    public function store(CreateInvoiceRequest $request){
        return $this->service->createinvoice([
            'invoicenumber'=>$request->invoicenumber,
            'regnumber'=>$request->regnumber,
            'itemcode'=>$request->inventoryItem,
            'currency'=>$request->currency,
            'amount'=>$request->amount,
        ]);
    }
    public function destroy($invoice_number){
        return $this->service->deleteinvoice($invoice_number);
    }
}
