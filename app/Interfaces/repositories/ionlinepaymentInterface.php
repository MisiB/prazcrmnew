<?php

namespace App\Interfaces\repositories;

interface ionlinepaymentInterface
{
    public function update(array $data);
    public function delete($id);
    public function getpayments($customer_id);
    public function getpayment($id);
    public function initiatepayment($data);
    public function checkpaymentstatus($uuid);
}
