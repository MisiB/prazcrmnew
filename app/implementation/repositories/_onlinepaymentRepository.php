<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\invoiceInterface;
use App\Interfaces\repositories\ionlinepaymentInterface;
use App\Interfaces\repositories\isuspenseInterface;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Onlinepayment;

class _onlinepaymentRepository implements ionlinepaymentInterface
{   
    /**
     * Create a new class instance.
     */
    protected $onlinepayment;
    protected $invoice;
    protected $currency;
    protected $customer;
    protected $invoicerepo;
    protected $suspenserepo;
    public function __construct(Onlinepayment $onlinepayment,Invoice $invoice,Currency $currency, Customer $customer,invoiceInterface $invoicerepo,isuspenseInterface $suspenserepo)
    {
        $this->onlinepayment = $onlinepayment;
        $this->invoice = $invoice;
        $this->currency = $currency;
        $this->customer = $customer;
        $this->invoicerepo = $invoicerepo;
        $this->suspenserepo = $suspenserepo;
    }

    public function getpayments($customer_id)
    {
        return $this->onlinepayment->with('currency')->where('customer_id', $customer_id)->paginate(10);
    }

    public function getpayment($id)
    {
        return $this->onlinepayment->with('currency','invoice','invoice.customer','invoice.inventoryitem')->where('id', $id)->first();
    }


    public function initiatepayment($data){
        try{
        $invoice = $this->invoicerepo->getInvoiceByInvoiceNumber($data['invoicenumber']);
        if($invoice==null){
            return ['status' => 'error', 'message' => 'Invoice not found'];
        }
        if(strtoupper($invoice->status)=="PAID"){
            return ['status' => 'error', 'message' => 'Invoice already settled'];
        }
        if($invoice->customer==null){
            return ['status' => 'error', 'message' => 'Customer account not found'];
        }
        if(strtoupper($invoice->customer->regnumber)!=$data['regnumber']){
            return ['status' => 'error', 'message' => 'Customer account not found'];
        }
     
        $totaldue  = $invoice->amount - $invoice->receipts->sum('amount');
        if($totaldue<=0){
            $invoice->status="PAID";
            $invoice->save();
            return ['status' => 'success', 'message' => 'Invoice settled successfully'];
        }
        $walletbalance = $this->suspenserepo->getwalletbalance($invoice->customer->regnumber,$invoice->inventoryitem->type,$invoice->currency->name);
        if($totaldue<=$walletbalance){
            return ['status'=>'error','message'=>'User has sufficient balance in wallet to settle invoice','data'=>null];
        }


        $paymentlink = config('paynowconfig.paymenturl')."/".$data["uuid"];
        $this->onlinepayment->create([
            "customer_id"=>$invoice->customer->id,
            "uuid"=>$data["uuid"],
            "currency_id"=>$invoice->currency->id,
            "amount"=>$totaldue,
            "email"=>$data["email"],            
            "invoicenumber"=>$data["invoicenumber"],
            "poll_url"=>"",
            "return_url"=>$data["returnurl"],
            "status"=>"PENDING"
        ]);
        return ['status'=>'success','message'=>'Payment link generated successfully','data'=>['link'=>$paymentlink]];
    }catch(\Exception $e){
        return ['status'=>'error','message'=>$e->getMessage()];
    }
}

    public function checkpaymentstatus($uuid){
        $payment = $this->onlinepayment->where('uuid',$uuid)->first();
        if($payment==null){
            return ['status'=>'error','message'=>'Transaction not found'];
        }
        if($payment->status=="PAID"){
            return ['status'=>'error','message'=>'Transaction already verified'];
        }
        $status = strtoupper($payment->status);
        if($status=="PAID" || $status=="AWAITING DELIVERY"){
            
        }
        return ['status'=>'success','message'=>'Payment found','data'=>$payment];
    }
 
    public function update(array $data){
        try{
            $this->onlinepayment->where('uuid',$data["uuid"])->update([
                "status"=>$data["status"],
                "poll_url"=>$data["poll_url"],
                "method"=>$data["method"],
                
            ]);
            return ['status'=>'success','message'=>'Payment updated successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    public function delete($id){
        try{
            $this->onlinepayment->where('id',$id)->delete();
            return ['status'=>'success','message'=>'Payment deleted successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
}
