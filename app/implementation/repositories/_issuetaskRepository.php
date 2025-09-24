<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\iissuetaskInterface;
use App\Models\Issuetask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class _issuetaskRepository implements iissuetaskInterface
{
    /**
     * Create a new class instance.
     */
    protected $task;
    public function __construct(Issuetask $task)
    {
        $this->task = $task;
    }
    public function getmytasks($year){
        $tasks = $this->task->with("user","individualoutputbreakdown")->where("user_id",Auth::user()->id)->whereYear("created_at",$year)->get();
        return $tasks;
    }
    public function gettask($id){
        $task = $this->task->with("user","individualoutputbreakdown")->find($id);
        return $task;
    }
    public function createtask($data){
        try{
            $uuid = Str::uuid();
            $this->task->create([
                "title"=>$data["title"],
                "user_id"=>$data["user_id"],
                "individualoutputbreakdown_id"=>$data["individualoutputbreakdown_id"],
                "contribution"=>$data["contribution"],
                "description"=>$data["description"],
                "start_date"=>$data["start_date"],
                "end_date"=>$data["end_date"],
                "priority"=>$data["priority"],                
                "created_by"=>Auth::user()->id,
                "uuid"=>$uuid
            ]);
            return ["status"=>"success","message"=>"Task created successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function updatetask($id,$data){
        try{
            $this->task->where("id",$id)->update([
                "title"=>$data["title"],
                "user_id"=>$data["user_id"],
                "individualoutputbreakdown_id"=>$data["individualoutputbreakdown_id"],
                "contribution"=>$data["contribution"],
                "description"=>$data["description"],
                "start_date"=>$data["start_date"],
                "end_date"=>$data["end_date"],
                "priority"=>$data["priority"],                
                "updated_by"=>Auth::user()->id
            ]);
            return ["status"=>"success","message"=>"Task updated successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function deletetask($id){
        try{
            $this->task->where("id","$id")->delete();
            return ["status"=>"success","message"=>"Task deleted successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }

    public function marktask($id,$status){
        try{
            $this->task->where("id","$id")->update([
                "status"=>$status,
                "updated_by"=>Auth::user()->id
            ]);
            return ["status"=>"success","message"=>"Task marked successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
    public function approvetask(array $data){
        try{
            $task = $this->task->where("id",$data['id'])->first();
            if($task->status != "Pending"){
                return ["status"=>"error","message"=>"You are not authorized to approve this task"];
            }
            if($data["status"] != "Approved"){
              $task->comment =  json_encode($data["comments"]);
            }else{
            $task->approval_status = $data["status"];
            $task->approved_by = Auth::user()->id;
            }
            $task->save();
            return ["status"=>"success","message"=>"Action successfully completed"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
}
