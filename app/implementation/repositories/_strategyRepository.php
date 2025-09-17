<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\istrategyInterface;
use App\Models\Strategy;
use App\Models\Strategyprogramme;
use App\Models\Strategyprogrammeoutcome;
use App\Models\Strategyprogrammeoutcomeindicator;
use App\Models\Strategysubprogramme;
use Illuminate\Support\Facades\Auth;

class _strategyRepository implements istrategyInterface
{
    /**
     * Create a new class instance.
     */
    protected $strategymodel;
    protected $strategyprogrammemodel;
    protected $strategyprogrammeoutcomemodel;
    protected $strategyprogrammeoutcomeindicator;
    protected $subprogramme;
    public function __construct(Strategy $strategymodel,Strategyprogramme $strategyprogrammemodel,Strategyprogrammeoutcome $strategyprogrammeoutcomemodel,Strategyprogrammeoutcomeindicator $strategyprogrammeoutcomeindicator,Strategysubprogramme $subprogrammemodel)
    {
        $this->strategymodel = $strategymodel;
        $this->strategyprogrammemodel = $strategyprogrammemodel;
        $this->strategyprogrammeoutcomemodel = $strategyprogrammeoutcomemodel;
        $this->strategyprogrammeoutcomeindicator = $strategyprogrammeoutcomeindicator;
        $this->subprogramme = $subprogrammemodel;
    }
    public function getstrategies()
    {
        return $this->strategymodel->with('approver','creator')->get();
    }
    public function getstrategy($id)
    {
        return $this->strategymodel->where('id',$id)->first();
    }
    public function createstrategy(array $data)
    {
        try {
            $this->strategymodel->create($data);
            return ["status"=>"success","message"=>"Strategy created successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function updatestrategy($id,array $data)
    {
        try {
             $checkstrategy = $this->strategymodel->where('id',$id)->first();
            if (!$checkstrategy) {
                return ["status"=>"error","message"=>"Strategy not found"];
            }
            if($checkstrategy->status != "Draft"){
                return ["status"=>"error","message"=>"You are not authorized to update this strategy"];
            }
            $this->strategymodel->where('id',$id)->update($data);
            return ["status"=>"success","message"=>"Strategy updated successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function deletestrategy($id)
    {
        try {
            $checkstrategy = $this->strategymodel->where('id',$id)->first();
            if (!$checkstrategy) {
                return ["status"=>"error","message"=>"Strategy not found"];
            }
            if($checkstrategy->status != "Draft"){
                return ["status"=>"error","message"=>"You are not authorized to delete this strategy"];
            }
            $this->strategymodel->where('id',$id)->delete();
            return ["status"=>"success","message"=>"Strategy deleted successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function approvestrategy($id)
    {
        try {
            $checkstrategy = $this->strategymodel->where('id',$id)->first();
            if (!$checkstrategy) {
                return ["status"=>"error","message"=>"Strategy not found"];
            }
            if($checkstrategy->status != "Draft"){
                return ["status"=>"error","message"=>"You are not authorized to approve this strategy"];
            }
            $this->strategymodel->where('id',$id)->update(['status'=>'Approved','approvedby'=>Auth::user()->id]);
            return ["status"=>"success","message"=>"Strategy approved successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function unapprovestrategy($id)
    {
        try {
            $checkstrategy = $this->strategymodel->where('id',$id)->first();
            if (!$checkstrategy) {
                return ["status"=>"error","message"=>"Strategy not found"];
            }
            if($checkstrategy->status != "Approved"){
                return ["status"=>"error","message"=>"You are not authorized to unapprove this strategy"];
            }
            $this->strategymodel->where('id',$id)->update(['status'=>'Draft','approvedby'=>Auth::user()->id]);
            return ["status"=>"success","message"=>"Strategy unapproved successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function copystrategy($id,$data)
    {
        try {
            $checkstrategy = $this->strategymodel->where('id',$id)->first();
            if (!$checkstrategy) {
                return ["status"=>"error","message"=>"Strategy not found"];
            }
            $this->strategymodel->where('id',$id)->first()->copy($data);
            return ["status"=>"success","message"=>"Strategy copied successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function getstrategybyuuid($uuid)
    {
        return $this->strategymodel->with('approver','creator','programmes')->where('uuid',$uuid)->first();
    }
    public function createstrategyprogramme(array $data)
    {
        try {
            $checkstrategy = $this->strategyprogrammemodel->where('strategy_id',$data['strategy_id'])->where('title',$data['title'])->first();
            if ($checkstrategy) {
                return ["status"=>"error","message"=>"Strategy programme already exists"];
            }
            $this->strategyprogrammemodel->create($data);
            return ["status"=>"success","message"=>"Strategy programme created successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function getprogramme($id)
    {
        return $this->strategyprogrammemodel->with(['outcomes.indicators.subprogrammes.department'])->where('id',$id)->first();
    }
    public function getprogrammebyuuid($uuid,$id)
    {
        return $this->strategyprogrammemodel->with(['strategy'])->whereHas('strategy',function($query)use($uuid){
            $query->where('uuid',$uuid);
        })->where('id',$id)->first();
    }
    public function updatestrategyprogramme($id,array $data)
    {
        try {
            $this->strategyprogrammemodel->where('id',$id)->update($data);
            return ["status"=>"success","message"=>"Strategy programme updated successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function deletestrategyprogramme($id)
    {
        try {
            $this->strategyprogrammemodel->where('id',$id)->delete();
            return ["status"=>"success","message"=>"Strategy programme deleted successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }

    public function getprogrammeoutcome($id)
    {
        return $this->strategyprogrammeoutcomemodel->with('indicators.subprogrammes.department')->where('id',$id)->first();
    }
    public function createstrategyprogrammeoutcome(array $data)
    {
        try {
            $this->strategyprogrammeoutcomemodel->create($data);
            return ["status"=>"success","message"=>"Strategy programme outcome created successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function updatestrategyprogrammeoutcome($id,array $data)
    {
        try {
            $this->strategyprogrammeoutcomemodel->where('id',$id)->update($data);
            return ["status"=>"success","message"=>"Strategy programme outcome updated successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function deletestrategyprogrammeoutcome($id)
    {
        try {
            $this->strategyprogrammeoutcomemodel->where('id',$id)->delete();
            return ["status"=>"success","message"=>"Strategy programme outcome deleted successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function getprogrammeoutcomebyuuid($programme_id,$outcome_id){
        return $this->strategyprogrammeoutcomemodel->with(['programme.strategy','indicators.subprogrammes'])->where('programme_id',$programme_id)->where('id',$outcome_id)->first();
    }
    
    public function addstrategycomments($id,array $data)
    {
        return $this->strategymodel->where('id',$id)->update($data);
    }
    public function updatestrategycomments($id,array $data)
    {
        return $this->strategymodel->where('id',$id)->update($data);
    }
    public function deletestrategycomments($id)
    {
        return $this->strategymodel->where('id',$id)->delete();
    }


    public function getprogrammeoutcomeindicator($id){
        return $this->strategyprogrammeoutcomeindicator->with('subprogrammes.department')->where('id',$id)->first();
    }
    public function createprogrammeoutcomeindicator(array $data){
        try {
           
            $this->strategyprogrammeoutcomeindicator->create($data);
            return ["status"=>"success","message"=>"Programme outcome indicator created successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function updateprogrammeoutcomeindicator($id,array $data){
        try {
            $this->strategyprogrammeoutcomeindicator->where('id',$id)->update($data);
            return ["status"=>"success","message"=>"Programme outcome indicator updated successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function deleteprogrammeoutcomeindicator($id){
        try {
            $this->strategyprogrammeoutcomeindicator->where('id',$id)->delete();
            return ["status"=>"success","message"=>"Programme outcome indicator deleted successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
  
    public function getsubprogramme($id){
        return $this->subprogramme->where('id',$id)->first();
    }
    public function createsubprogramme(array $data){
        try {
            $check = $this->subprogramme->where('programmeoutcomeindicator_id',$data['programmeoutcomeindicator_id'])->where('department_id',$data['department_id'])->first();
            if($check){
                return ["status"=>"error","message"=>"Subprogramme already exists"];
            }
            $totalweightage = $this->subprogramme->where('programmeoutcomeindicator_id',$data['programmeoutcomeindicator_id'])->sum('weightage');
            if($totalweightage+$data['weightage']>100){
                return ["status"=>"error","message"=>"Total weightage cannot exceed 100"];
            }
            $data['createdby']=Auth::user()->id;
            $this->subprogramme->create($data);
            return ["status"=>"success","message"=>"Subprogramme created successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function updatesubprogramme($id,array $data){
        try {
            $totalweightage = $this->subprogramme->where('programmeoutcomeindicator_id',$data['programmeoutcomeindicator_id'])->sum('weightage');
            if($totalweightage+$data['weightage']>100){
                return ["status"=>"error","message"=>"Total weightage cannot exceed 100"];
            }
            $this->subprogramme->where('id',$id)->first()->update($data);
            return ["status"=>"success","message"=>"Subprogramme updated successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
   
    public function deletesubprogramme($id){
        try {
            $this->subprogramme->where('id',$id)->first()->delete();
            return ["status"=>"success","message"=>"Subprogramme deleted successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
}
