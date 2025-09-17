<?php

namespace App\implementation\services;

use App\Interfaces\repositories\isuspenseInterface;
use App\Interfaces\services\isuspenseService;

class _suspenseService implements isuspenseService
{
    /**
     * Create a new class instance.
     */
    protected $repository;
    public function __construct(isuspenseInterface $repository)
    {
        $this->repository = $repository;
    }
    public function getpendingsuspensewallets(){
        return $this->repository->getpendingsuspensewallets();
    }
    public function create(array $data){
        return $this->repository->create($data);
    }
    public function createmonthlysuspensewallets($month,$year){
        return $this->repository->createmonthlysuspensewallets($month,$year);
    }
    public function getmonthlysuspensewallets($month,$year){
        return $this->repository->getmonthlysuspensewallets($month,$year);
    }
    public function getsuspensewallet($regnumber){
        return $this->repository->getsuspensewallet($regnumber);
    }
    public function getsuspense($id){
        return $this->repository->getsuspense($id);
    }
    public function getsuspensestatement($customer_id){
        return $this->repository->getsuspensestatement($customer_id);
    }
    public function getwalletbalance($regnumber,$accounttype,$currency){
        return $this->repository->getwalletbalance($regnumber,$accounttype,$currency);
    }
    public function deductwallet($regnumber,$invoice_id,$accounttype,$currency,$amount,$receiptnumber){
        return $this->repository->deductwallet($regnumber,$invoice_id,$accounttype,$currency,$amount,$receiptnumber);
    }
}
