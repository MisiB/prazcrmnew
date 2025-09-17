<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\iepaymentInterface;
use App\Models\Epayment;

class _epaymentRepository implements iepaymentInterface
{
    /**
     * Create a new class instance.
     */
    protected $modal;
    public function __construct(Epayment $modal)
    {
        $this->modal = $modal;
    }

    public function getepayments($customer_id){
        return $this->modal->where('customer_id', $customer_id)->paginate(10);
    }
}
