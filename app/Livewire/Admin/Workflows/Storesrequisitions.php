<?php

namespace App\Livewire\Admin\Workflows;

use App\Interfaces\repositories\ihodstoresrequisitionapprovalInterface;
use App\Interfaces\repositories\istoresrequisitionInterface;
use App\Interfaces\repositories\iuserInterface;
use App\Notifications\StoresrequisitionapprovalSubmitted;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class Storesrequisitions extends Component
{
    Use Toast;

    protected $user, $storesrequisitionrepo, $hodstoresrequisitionapprovalrepo, $userrepo;
    public $breadcrumbs = [];
    public $totalapproved=0, $totalpending=0, $totalrejected=0;
    public $statuslist =[];
    public $addrequisitionmodal=false;
    public $itemdetail, $requiredquantity, $purposeofrequisition, $employeesignature;
    public $hodid;

    public function boot(istoresrequisitionInterface $storesrequisitionrepo, ihodstoresrequisitionapprovalInterface $hodstoresrequisitionapprovalrepo, iuserInterface $userrepo)
    {
        $this->storesrequisitionrepo=$storesrequisitionrepo;
        $this->hodstoresrequisitionapprovalrepo=$hodstoresrequisitionapprovalrepo;
        $this->userrepo=$userrepo;
        $this->user=Auth::user();
    }

    public function mount()
    {
        $this->breadcrumbs=[
            ['label' => 'Home', 'link' => route('admin.home')],
            ['label' => "Stores Requisition"]
        ];
       
        $this->hodid=$this->user->department->reportto;
    }

    public function headers(): array
    { 
        return [
            ['label' => 'Item detail', 'key' => 'itemdetail'],
            ['label' => 'Required Quantity', 'key' => 'requiredquantity'],
            ['label' => 'Initiator', 'key' => 'initiator'],
            ['label' => 'Status', 'key' => 'status']
        ];
    }
    public function getmystoresrequests()
    { 
        return $this->storesrequisitionrepo->getstoresrequisitions();
    }

    public function sendrequisition()
    {
        $this->validate([
            'itemdetail'=>'required',
            'requiredquantity'=>'required|numeric',
            'purposeofrequisition'=>'required',
            'employeesignature'=>'required',
        ]);
        $storesrequuid= (string) Str::uuid();
        $createrequisition=$this->storesrequisitionrepo->createstoresrequisition([
            'storesrequisition_uuid'=>$storesrequuid,
            'itemdetail'=>$this->itemdetail,
            'requiredquantity'=>$this->requiredquantity,
            'purposeofrequisition'=>$this->purposeofrequisition,
            'status'=>'P',
            'initiator_id'=>$this->user->id,
            'initiatorsignature'=>$this->employeesignature,
        ]);
        if($createrequisition['status']=='success')
        {
            $createrequisition=$this->hodstoresrequisitionapprovalrepo->createhodrequisitionapproval([
                'storesrequisition_uuid'=>$storesrequuid,
                'user_id'=>$this->hodid
            ]);
            //send email notification to approver
            $approver=$this->userrepo->getuser($this->hodid);
            $approver->notify(new StoresrequisitionapprovalSubmitted($this->storesrequisitionrepo, $this->hodstoresrequisitionapprovalrepo, $storesrequuid) );
            $this->toast($createrequisition['status'], $createrequisition['message']);
            $this->addrequisitionmodal=false;
            return redirect('/workflows/storesrequisitions');
        }
        return $this->toast($createrequisition['status'], $createrequisition['message']);
    }

    public function render()
    {
        return view('livewire.admin.workflows.storesrequisitions',[
            'headers' => $this->headers(),
            'storesrequisitions'=>$this->getmystoresrequests(),
        ]);
    }
}
 