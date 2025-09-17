<?php

namespace App\implementation\services;

use App\Interfaces\repositories\ipaynowintegrationsInterface;
use App\Interfaces\services\ipaynowInterface;
use Paynow\Payments\Paynow;

class _paynowService implements ipaynowInterface
{
    /**
     * Create a new class instance.
     */
   protected $paynowintegrationsrepo;

    public function __construct(ipaynowintegrationsInterface $paynowintegrationsrepo)
    {
        $this->paynowintegrationsrepo = $paynowintegrationsrepo;
    }

    public function initiatepayment($data){
          }

    public function checkpaymentstatus($data){
        $paynowintegrations = $this->paynowintegrationsrepo->getpaynowparameters(["type"=>$data["type"],"currency_id"=>$data["currency_id"]]);
        if($paynowintegrations==null){
            return ['status'=>'error','message'=>'Paynow integration not found'];
        }
        $paynow = new Paynow($paynowintegrations->key, $paynowintegrations->token,$data["returnurl"],$data["returnurl"]);
        $status = $paynow->pollTransaction($data["pollurl"]);
        if($status->paid()){
            return ['status'=>'PAID'];
        }
        return ['status'=>$status->status()];
    }
}
