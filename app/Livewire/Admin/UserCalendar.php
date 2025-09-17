<?php

namespace App\Livewire\Admin;

use App\Interfaces\repositories\itaskInterface;
use App\Interfaces\repositories\iworkplanInterface;
use App\Interfaces\services\ICalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class UserCalendar extends Component
{
    use Toast;
    protected $repository;
    protected $workplanrepository;
    public $startDate;
    public $endDate;
    public $currentweek=null;
    public $modal=false;
    public $currentday=null;
    public $title;
    public $priority;
    public $year;
    public $individualoutputbreakdown_id;
    public $description;
    public $status;
    public $start_date;
    public $end_date;
    public bool $link = false;
    public $id;
    public $selectedtask=null;
    public $viewtaskmodal=false;
    public $markmodal=false;
    public $taskid;
    public $week_id;
    public bool $viewcommentmodal=false;
    protected $calendarService;

    public function boot(ICalendarService $calendarService,itaskInterface $repository,iworkplanInterface $workplanrepository)
    {
        $this->calendarService = $calendarService;
        $this->repository = $repository;
        $this->workplanrepository = $workplanrepository;
    }
    public function mount()
    {
        $this->getcalenderuserweektasks();
        $this->year = Carbon::now()->year;
    }

    public function getcalenderuserweektasks()
    {
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        $this->currentweek= $this->calendarService->getcalenderuserweektasks($this->startDate, $this->endDate);
   
    }
    public function getcalenderuserweektasksbyweekid()
    {
        $this->currentweek= $this->calendarService->getusercalendarweektasks($this->week_id);
    }
    public function updatedweekid(){
        $this->getcalenderuserweektasksbyweekid();
    }

    public function getweeks(){
        return $this->calendarService->getweeks($this->year);
    }
    public function openModal($day)
    {
        $this->currentday = $day;
        $this->modal = true;
    }



    public function getmyworkplanbreakdownlist(){
        return $this->workplanrepository->getworkplabreakdownbyuser(Auth::user()->id,$this->year);
    }

    public function statuslist():array{
        return [
            ["id"=>"pending","name"=>"Pending"],
            ["id"=>"ongoing","name"=>"Ongoing"],
            ["id"=>"completed","name"=>"Completed"]
        ];
    }

    public function prioritylist():array{
        return [
            ["id"=>"High","name"=>"High"],
            ["id"=>"Medium","name"=>"Medium"],
            ["id"=>"Low","name"=>"Low"]
        ];
    }


    public function save(){
        $this->validate([
            "title"=>"required",
            "priority"=>"required",
            "description"=>"required",
            'individualoutputbreakdown_id'=>'required_if:link,true',

        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset([
            "title",
            "priority",
            "description",
            "link",
            "individualoutputbreakdown_id",
            "id"
        ]);
    }

    public function create(){
      $response =  $this->repository->createtask([
            "title"=>$this->title,
            "priority"=>$this->priority,
            "description"=>$this->description,
            'calendarday_id'=>$this->currentday,
            "user_id"=>Auth::user()->id,
            "individualoutputbreakdown_id"=>$this->individualoutputbreakdown_id
        ]);

        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }

    public function edit($id){
      
        $task = $this->repository->gettask($id); 
        $this->id = $task->id;
        $this->title = $task->title;
        $this->currentday = $task->calendarday_id;
        $this->priority = $task->priority;
        $this->individualoutputbreakdown_id = $task->individualoutputbreakdown_id;
        $this->description = $task->description;
        $this->status = $task->status;
        $this->modal=true;
    }

    public function update(){
        $response= $this->repository->updatetask($this->id,[
            "title"=>$this->title,
            "priority"=>$this->priority,
            "description"=>$this->description,
            "calendarday_id"=>$this->currentday,
            "user_id"=>Auth::user()->id,
            "individualoutputbreakdown_id"=>$this->individualoutputbreakdown_id
        ]);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }
    public function delete($id){
        $response = $this->repository->deletetask($id);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }

    public function viewtask($id){
        $this->selectedtask = null;
         $task =$this->repository->gettask($id);
         $this->selectedtask = $task;
        $this->viewtaskmodal=true;
    }

    public function openmarkmodal($id){
        $this->taskid = $id;
        $this->markmodal=true;
    }
    public function marktask($id){
        $response = $this->repository->marktask($id,"completed");
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }
    public function marktaskasongoing($id){
        $response = $this->repository->marktask($id,"ongoing");
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
        $this->markmodal=false;
    }

    public function marktaskaspending($id){
        $response = $this->repository->marktask($id,"pending");
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
        $this->markmodal=false;
    }
    public function marktaskascompleted($id){
        $response = $this->repository->marktask($id,"completed");
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
        $this->markmodal=false;
    }

    public function sendforapproval(){
        $response = $this->calendarService->sendforapproval($this->currentweek->id);
        if($response["status"] == "success"){
            $this->success($response["message"]);
        }else{
            $this->error($response["message"]);
        }
    }

    public function render()
    {
        return view('livewire.admin.user-calendar',[
            'breakdownlist'=>$this->getmyworkplanbreakdownlist(),
            'statuslist'=>$this->statuslist(),
            'prioritylist'=>$this->prioritylist(),
            'weeks'=>$this->getweeks(),
        ]);
    }
}
