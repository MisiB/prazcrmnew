<?php

namespace App\Livewire\Admin\Management;

use App\Interfaces\repositories\istrategyInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class Strategydetail extends Component
{
    use Toast;
    public $uuid;
    public $id;
    public $title;
    public $code;
    public $programme_id;
    public bool $modal = false;
    public bool $viewModal = false;
    public $strategy = null;
    public $breadcrumbs = [];
    public array $myChart = [];
    public $programme = null;
    public $outcome_id;
    public $outcomemodal = false;
    public $indicatormodal = false;
    public $indicator_id;
    public $outcome = null;

    protected $strategyRepository;
    public function boot(istrategyInterface $strategyRepository)
    {
        $this->strategyRepository = $strategyRepository;
    }
  public function mount($uuid)
  {
    $this->uuid = $uuid;
    $this->getstrategybyuuid();
    $this->breadcrumbs = [
        [
            'label' => 'Strategies',
            'link' => route('admin.management.strategies'),
        ],
        [
            'label' => $this->strategy->name ?? 'Strategy',
        ],
    ];
    $this->myChart = [
        'type' => 'pie',
        'data' => [
            'labels' => ['Mary', 'Joe', 'Ana'],
            'datasets' => [
                [
                    'label' => '# of Votes',
                    'data' => [12, 19, 3],
                ]
            ]
        ]
    ];
  }
  public function getstrategybyuuid()
  {
    $payload = $this->strategyRepository->getstrategybyuuid($this->uuid);
    if($payload)
    {
      $this->strategy = $payload;
    }
    else{
        return redirect()->route('admin.management.strategies')->with('error','Strategy not found');
    }
    
  }

  public function save(){
    $this->validate([
        "title"=>"required",
    ]);
    
     if($this->id){
        
        $response = $this->strategyRepository->updatestrategyprogramme($this->id, [
            "strategy_id"=>$this->strategy->id,
            "title"=>$this->title,
            "code"=>$this->code,
            "updatedby"=>Auth::user()->id
        ]);
        if($response['status']=="success")
        {
           $this->success($response['message']);
        }
        else{
          $this->error($response['message']);
        }
     }
     else{
        $response = $this->strategyRepository->createstrategyprogramme([
            "strategy_id"=>$this->strategy->id,
            "title"=>$this->title,
            "code"=>$this->code,
            "createdby"=>Auth::user()->id
        ]);
        if($response['status']=="success")
        {
          $this->success($response['message']);
        }
        else{
          $this->error($response['message']);
        }
     }
     $this->reset([
        "title",
        "id"
        ]);
        $this->closeModal();
  }

  public function openModal(){
    $this->modal = true;
  }

  public function closeModal(){
    $this->modal = false;
  }
  public function getprogramme($id){
    $this->id = $id;
    $programme = $this->strategyRepository->getprogramme($id);
    if($programme){
      $this->title = $programme->title;
      $this->code = $programme->code;
    }
    $this->openModal();
  }
  public function getoutputs(){
    $this->programme = $this->strategyRepository->getprogramme($this->programme_id);
  }
  public function openViewModal($id){
  
    $this->viewModal = true; 
   
    $this->programme_id = $id;
    $this->getoutputs();
  }
  public function closeViewModal(){
    $this->viewModal = false;
  }

  public function deleteprogramme($id){
    $response = $this->strategyRepository->deletestrategyprogramme($id);
    if($response['status']=="success")
    {
      $this->success($response['message']);
    }
    else{
      $this->error($response['message']);
    }
  }

  public function saveoutcome(){
        $data = [
            'title'=>$this->title,
            "createdby"=>Auth::user()->id,
            'programme_id'=>$this->programme_id,
        ];
        if($this->outcome_id){
            $data['updatedby'] = Auth::user()->id;
           $response = $this->strategyRepository->updatestrategyprogrammeoutcome($this->outcome_id,$data);
           if($response['status']=='success'){
            $this->getoutputs();
            $this->success($response['message']);
           }
           else{
            $this->error($response['message']);
           }
        }
        else{
            $response = $this->strategyRepository->createstrategyprogrammeoutcome($data);
            if($response['status']=='success'){
                $this->getoutputs();
                $this->success($response['message']);
            }
            else{
                $this->error($response['message']);
            }
        }
        $this->reset([
            'title',
            'id'
            ]);
    }

    public function editoutcome($id){
        $this->outcome_id = $id;
        $outcome = $this->strategyRepository->getprogrammeoutcome($id);
        if($outcome){
            $this->title = $outcome->title;
            $this->outcomemodal = true;
        }
    }
    public function deleteprogrammeoutcome($id){
        $response = $this->strategyRepository->deletestrategyprogrammeoutcome($id);
        if($response['status']=='success'){
            $this->getoutputs();
            $this->success($response['message']);
        }
        else{
            $this->error($response['message']);
        }
    }

    public function getinidicators($id){
        $this->outcome_id = $id;
        $outcome = $this->strategyRepository->getprogrammeoutcome($id);
        if($outcome){
            $this->outcome = $outcome;
            $this->indicatormodal = true;
        }
    }

    public function saveindicator(){
        $this->validate([
            'title'=>'required',
        ]);
        $data = [
            'indicator'=>$this->title,
            'programmeoutcome_id'=>$this->outcome_id,
            'createdby'=>Auth::user()->id,
        ];
        if($this->indicator_id){
            $data['updatedby'] = Auth::user()->id;
            $response = $this->strategyRepository->updateprogrammeoutcomeindicator($this->indicator_id,$data);
            if($response['status']=='success'){
                $this->getoutputs();
                $this->success($response['message']);
            }
            else{
                $this->error($response['message']);
            }
        }
        else{
            $response = $this->strategyRepository->createprogrammeoutcomeindicator($data);
            if($response['status']=='success'){
                $this->getoutputs();
                $this->success($response['message']);
            }
            else{
                $this->error($response['message']);
            }
        }
    }
    public function editindicator($id){
        $this->indicator_id = $id;
        $indicator = $this->strategyRepository->getprogrammeoutcomeindicator($id);
        if($indicator){
            $this->title = $indicator->title;
        }
    }

    public function render()
    {
        return view('livewire.admin.management.strategydetail');
    }
}
