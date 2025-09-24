<?php

namespace App\Interfaces\repositories;

interface ileaverequestapprovalInterface
{
    public function getleaverequestapprovals();
    public function getleaverequestapprovalsbyuserid($userid);
    public function getleaverequestapprovalsbystatus($status);
    public function getleaverequestapproval($requestuuid);
    public function createleaverequestapproval($data);
    public function updateleaverequestapproval($uuid, $data);
    public function deleteleaverequestapproval($id);
}