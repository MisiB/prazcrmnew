<?php

namespace App\Interfaces\repositories;

interface istoresrequisitionInterface
{
    public function getstoresrequisitions();
    public function getstoresrequisitionsByStatus($status);
    public function getstoresrequisitionsByDepartment($departmentid);
    public function getstoresrequisition($storesrequisitionuuid);
    public function createstoresrequisition($data);
    public function updatestoresrequisition($id, $data);
    public function deletestoresrequisition($id);
}
