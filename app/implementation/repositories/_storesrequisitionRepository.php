<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\istoresrequisitionInterface;
use App\Models\Departmentuser;
use App\Models\Storesrequisition;

class _storesrequisitionRepository implements istoresrequisitionInterface
{
    protected $model, $deptmodel;
    public function __construct(Storesrequisition $model, Departmentuser $deptmodel)
    {
       $this->model=$model;
       $this->deptmodel=$deptmodel;
    }

    public function getstoresrequisitions()
    {
        return $this->model->all();
    }
    public function getstoresrequisitionsByStatus($status)
    {
        return $this->model->where('status', $status)->first();
    }
    public function getstoresrequisitionsByDepartment($departmentid)
    {
        return $this->model->whereHas('initiator', function($query) use ($departmentid)
        {
            return $this->deptmodel->where('user_id', $query->id)->where('department_id', $departmentid);
        });
    }
    public function getstoresrequisition($storesrequisitionuuid)
    {
        return $this->model->where('storesrequisition_uuid', $storesrequisitionuuid)->first();
    }
    public function createstoresrequisition($data)
    {
        try
        {
            $check = $this->getstoresrequisition($data['storesrequisition_uuid']);
            if ($check) {
                return ['status' => "error", 'message' => 'Store requisition already exists'];
            }
            $this->model->create($data);
            return ['status' => "success", 'message' => 'Store requisition created successfully'];
        } catch (\Exception $e) {
            return ['status' => "error", 'message' => $e->getMessage()];
        }
    }
    public function updatestoresrequisition($storesrequisition_uuid, $data)
    {
        try
        {
            $check = $this->$this->getstoresrequisition($data['storesrequisition_uuid']);
            if (!$check) {
                return ['status' => "error", 'message' => 'Store requisition not found'];
            }
            $check->update($data);
            return ['status' => "success", 'message' => 'Store requisition updated successfully'];
        } catch (\Exception $e) {
            return ['status' => "error", 'message' => $e->getMessage()];
        }
    }
    public function deletestoresrequisition($id)
    {
        try
        {
            $check = $this->model->find($id);
            if (!$check) {
                return ['status' => "error", 'message' => 'Store requisition not found'];
            }
            $check->delete();
            return ['status' => "success", 'message' => 'Store requisition deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => "error", 'message' => $e->getMessage()];
        }
    }
}
