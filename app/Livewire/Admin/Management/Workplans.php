<?php

namespace App\Livewire\Admin\Management;

use App\Interfaces\repositories\istrategyInterface;
use App\Interfaces\repositories\iworkplanInterface;
use App\Interfaces\repositories\idepartmentInterface;
use Carbon\Carbon;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;

class Workplans extends Component
{
    use Toast;
    protected $repo;
    protected $strategyrepo;
    protected $departmentrepo;
    public $year;
    public $strategy_id;
    public $workplans;
    public array  $breadcrumbs=[];
    public $modal=false;
    public bool $addmodal = false;
    public $id;
    public $output;
    public  $indicator;
    public  $target;
    public  $variance;
    public $weightage;
    public $parent_id;
    public $subprogrammeoutput_id;
    public  $workplan = null;
    public $Outputindicators = [];
    public $subordinates;
    public $assigneelist;
    public $assignemodal = false;
    public $newassignemodal = false;
    public $contribution;
    public $selected_id;
    public $subordinate_id;
    public $individualoutput_id;
    public  $assignee_id;
    public $user_id;
    public $breakdown_id;
    public $month;
    public $description;
    public $breakdownmodal = false;
    public $addbreakdownmodal = false;
    public $breakdownlist;
    public function boot(istrategyInterface $strategyrepo,iworkplanInterface $repo,idepartmentInterface $departmentrepo)
    {
      $this->strategyrepo = $strategyrepo;
      $this->repo = $repo;
      $this->departmentrepo = $departmentrepo;
    }

    public function mount(){
        $this->year = Carbon::now()->year;
        $this->strategy_id = $this->getstrategies()->first()->id;
        $this->subordinates = new Collection();
        $this->assigneelist = new Collection();
        $this->breakdownlist = new Collection();
        $this->workplans = new Collection();
        $this->breadcrumbs = [
            [
                'label' => 'Home',
                'link' => route('admin.home'),
            ],
            [
                'label' => 'Workplans',
            ],
        ];
        $this->getworkplans();
    }
    public function getstrategies(){
        $data = $this->strategyrepo->getstrategies();
      
        return $data;
    }
    public function getworkplans(){
        $this->validate([
            "strategy_id"=>"required",
            "year"=>"required"
        ]);
        $workplans = $this->repo->getworkplans($this->strategy_id,$this->year);
      
        $this->workplans = collect($workplans);
    }

    public function addworkplan($subprogrammeoutput_id){
        $this->subprogrammeoutput_id = $subprogrammeoutput_id;  
        $workplan = $this->workplans->where("subprogrammeoutput_id", $subprogrammeoutput_id)->first();
        if($workplan && $workplan["supervisoroutput_id"]){
            $this->parent_id = $workplan["supervisoroutput_id"];      
        }
        $this->addmodal = true;
    }

    public function save(){
        $this->validate([
            "output"=>"required",
            "indicator"=>"required",
            "target"=>"required",
            "variance"=>"required",
            "weightage"=>"required",
        ]);
        if($this->id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset(["output","indicator","target","variance","weightage"]);
    
    }
    public function editoutput($id){
        $workplan = $this->repo->getworkplan($id);
        $this->id = $workplan->id;
        $this->output = $workplan->output;
        $this->indicator = $workplan->indicator;
        $this->target = $workplan->target;
        $this->variance = $workplan->variance;
        $this->weightage = $workplan->weightage;
        $this->parent_id = $workplan->parent_id;
        $this->subprogrammeoutput_id = $workplan->subprogrammeoutput_id;
        $this->addmodal = true;
    }

    public function create(){
       $response = $this->repo->createworkplan([
            "output"=>$this->output,
            "indicator"=>$this->indicator,
            "target"=>$this->target,
            "variance"=>$this->variance,
            "weightage"=>$this->weightage,
            "parent_id"=>$this->parent_id,
            "subprogrammeoutput_id"=>$this->subprogrammeoutput_id
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->addmodal = false;
            $this->getworkplans();
        }else{
            $this->error($response['message']);
        }

    }
    public function update(){
        $response = $this->repo->updateworkplan($this->id, [
            "output"=>$this->output,
            "indicator"=>$this->indicator,
            "target"=>$this->target,
            "variance"=>$this->variance,
            "parent_id"=>$this->parent_id,
            "weightage"=>$this->weightage,
            "subprogrammeoutput_id"=>$this->subprogrammeoutput_id
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->addmodal = false;
            $this->getworkplans();
        }else{
            $this->error($response['message']);
        }
    }
    public function deleteoutput($id){
        $response = $this->repo->deleteworkplan($id);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->getworkplans();
        }else{
            $this->error($response['message']);
        }
    }
    public function getsubordinates($id){
        $this->individualoutput_id = $id;
        $this->subordinates = $this->departmentrepo->getmysubordinates();
        $this->getassignees();
        $this->assignemodal = true;

    }
    public function getassignees(){
        $this->assigneelist = $this->repo->getworkplanassignees($this->individualoutput_id);
    }
    public function selectassign($id){
        $this->newassignemodal = true;
        $this->user_id = $id;
    }
    public function saveassignee(){
        $this->validate([
            "target"=>"required",
        ]);
       
        if($this->assignee_id){
            $this->updateassignee();
        }else{
            $this->createassignee();
        }
        $this->reset([
            "assignee_id",
            "user_id",
            "target",
            "variance"
        ]);
        $this->getassignees();
    }
    public function createassignee(){
        $this->user_id = $this->subordinates->where("id", $this->user_id)->first()->user_id;
        $response = $this->repo->createworkplanassignee([
            "individualoutput_id"=>$this->individualoutput_id,
            "user_id"=>$this->user_id,
            "target"=>$this->target,
            "variance"=>$this->variance
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->newassignemodal = false;
        }else{
            $this->error($response['message']);
        }
    }
    public function editassign($id){
        $assignee = $this->repo->getworkplanassignee($id);
        $this->assignee_id = $assignee->id;
        $this->user_id = $assignee->user_id;
        $this->target = $assignee->target;
        $this->newassignemodal = true;
    }
    public function updateassignee(){
        $response = $this->repo->updateworkplanassignee($this->assignee_id, [
            "target"=>$this->target,
            "variance"=>$this->variance
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->newassignemodal = false;
        }else{
            $this->error($response['message']);
        }
    }
    public function deleteassignee($id){
        $response = $this->repo->deleteworkplanassignee($id);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->getassignees();
        }else{
            $this->error($response['message']);
        }
    }

    public function getbreakdown($id){
        $this->individualoutput_id = $id;
        $this->breakdownlist = $this->repo->getworkplanbreakdownlist($id);
        $this->breakdownmodal = true;
    }

    public function savebreakdown(){
        $this->validate([
            "month"=>"required",
            "contribution"=>"required",
            "description"=>"required",
            "output"=>"required",
        ]);
        if($this->breakdown_id){
            $this->updatebreakdown();
        }else{
            $this->createbreakdown();
        }
        $this->reset([
            "month",
            "contribution",
            "description",
            "output"
        ]);        
    }
    public function createbreakdown(){
        $response=$this->repo->createworkplanbreakdown([
            "individualoutput_id"=>$this->individualoutput_id,
            "month"=>$this->month,
            "contribution"=>$this->contribution,
            "description"=>$this->description,
            "output"=>$this->output
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->breakdownlist = $this->repo->getworkplanbreakdownlist($this->individualoutput_id);
        }else{
            $this->error($response['message']);
        }
    }
    public function editbreakdown($id){
        $breakdown = $this->repo->getworkplanbreakdown($id);
        $this->breakdown_id = $breakdown->id;
        $this->month = $breakdown->month;
        $this->contribution = $breakdown->contribution;
        $this->description = $breakdown->description;
        $this->output = $breakdown->output;
        $this->addbreakdownmodal = true;
    }
    public function updatebreakdown(){
        $response = $this->repo->updateworkplanbreakdown($this->breakdown_id, [
            "month"=>$this->month,
            "contribution"=>$this->contribution,
            "description"=>$this->description,
            "output"=>$this->output
        ]);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->addbreakdownmodal = false;
            $this->breakdownlist = $this->repo->getworkplanbreakdownlist($this->individualoutput_id);
        }else{
            $this->error($response['message']);
        }
    }
    public function deletebreakdown($id){
        $response = $this->repo->deleteworkplanbreakdown($id);
        if($response['status'] == "success"){
            $this->success($response['message']);
            $this->breakdownlist = $this->repo->getworkplanbreakdownlist($this->individualoutput_id);
        }else{
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.management.workplans',[
            "strategies"=>$this->getstrategies()
            ]);
    }
}
