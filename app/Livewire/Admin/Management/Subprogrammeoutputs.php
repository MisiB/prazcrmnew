<?php

namespace App\Livewire\Admin\Management;

use App\Interfaces\repositories\istrategyInterface;
use App\Interfaces\repositories\isubprogrammeoutInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Subprogrammeoutputs extends Component
{
    use Toast;
    protected $repo;
    protected $strategyrepo;
    public $strategy_id;
    public $year;
    public $subprogrammeoutputs;
    public  $breadcrumbs=[];
    public $modal=false;
    public $output;
    public $indicator;
    public $quantity;
    public $target;
    public $variance;
    public $subprogramme_id;
    public $createModal = false;
    public $output_id;
    public function boot(isubprogrammeoutInterface $repo,istrategyInterface $strategyrepo)
    {
        $this->repo = $repo;
        $this->strategyrepo = $strategyrepo;
    }
    public function mount(){
        $this->year = date("Y");
        $this->subprogrammeoutputs = new Collection();
        $this->getlatestsubprogrammeoutput();
        $this->breadcrumbs = [
            [
                'label' => 'Home',
                'link' => route('admin.home'),
            ],
            [
                'label' => 'Subprogramme Outputs',
            ],
        ];
    }

    public function getstrategies(){
        return $this->strategyrepo->getstrategies();
    }
    public function getsubprogrammeoutputs(){
        $this->validate([
            "strategy_id"=>"required",
            "year"=>"required"
        ]);
      $subprogrammeoutputs = $this->repo->getsubprogrammeoutputs($this->strategy_id,$this->year);
   
      $this->subprogrammeoutputs = collect($subprogrammeoutputs);
    }

    public function getlatestsubprogrammeoutput(){
        $strategy_id = $this->getstrategies()->last()->id;
        $year = Carbon::now()->year;
        $this->strategy_id = $strategy_id;
        $subprogrammeoutputs = $this->repo->getsubprogrammeoutputs($strategy_id,$year);
        $this->subprogrammeoutputs = collect($subprogrammeoutputs);
    }
        

    public function addoutput($subprogramme_id){
        $this->subprogramme_id = $subprogramme_id;
        $this->createModal = true;
    }


    public function save(){ 
        $this->validate([
            "output"=>"required",
            "indicator"=>"required",
            "quantity"=>"required",
            "target"=>"required",
            "variance"=>"required"
        ]);
        if($this->output_id){
            $this->update();
        }else{
            $this->create();
        }
        $this->reset([
            "indicator",
            "output",
            "quantity",
            "target",
            "variance",
            "output_id"
        ]);
        
      

    }
    public function getoutput($id){
        $output = $this->repo->getsubprogrammeoutput($id);
        if($output){
            $this->output = $output->output;
            $this->indicator = $output->indicator;

            $this->quantity = $output->quantity;
            $this->target = $output->target;
            $this->variance = $output->variance;
            $this->output_id = $output->id;
            $this->subprogramme_id = $output->subprogramme_id;
            $this->createModal = true;
        }
    }

    public function create(){
      $response = $this->repo->createsubprogrammeoutput([
        "subprogramme_id"=>$this->subprogramme_id,
        "output"=>$this->output,
        'strategy_id'=>$this->strategy_id,
        'department_id'=>Auth::user()->department->department_id,
        "indicator"=>$this->indicator,
        "quantity"=>$this->quantity,
        "target"=>$this->target,
        "variance"=>$this->variance
      ]);
      if($response["status"] == "success"){
        $this->getlatestsubprogrammeoutput();
       $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    public function update(){
      $response = $this->repo->updatesubprogrammeoutput($this->output_id,[
        "output"=>$this->output,
        'strategy_id'=>$this->strategy_id,
        'department_id'=>Auth::user()->department->department_id,
        "indicator"=>$this->indicator,
        "quantity"=>$this->quantity,
        "target"=>$this->target,
        "variance"=>$this->variance,
        "subprogramme_id"=>$this->subprogramme_id
      ]);
      if($response["status"] == "success"){
       $this->getlatestsubprogrammeoutput();
       $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    public function deleteoutput($id){
      $response = $this->repo->deletesubprogrammeoutput($id);
      if($response["status"] == "success"){
       $this->getlatestsubprogrammeoutput();
       $this->success($response["message"]);
      }else{
        $this->error($response["message"]);
      }
    }
    public function render()
    {
        return view('livewire.admin.management.subprogrammeoutputs',[
            "strategies"=>$this->getstrategies()
        ]);
    }
}
