<?php

namespace App\Interfaces\repositories;

interface ileavestatementInterface
{
    public function getleavestatements();
    public function getleavestatementbyleavetype($leavetypeid);
    public function getleavestatementbyuser($userid);
    public function getleavestatement($id);
    public function createleavestatement($data);
    public function updateleavestatement($id, $data);
    public function deleteleavestatement($id);
    public function getleavestatementbyuserandleavetype($userid, $leavetypeid);
    public function getleavestatementbyuseridandleavename($userid, $leavename);
}
 