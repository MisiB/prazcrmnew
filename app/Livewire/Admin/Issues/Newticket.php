<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iservicecustomerInterface;
use App\Models\Issuegroup;
use App\Models\Issuelog;
use App\Models\Issuetype;
use App\Notifications\NewticketNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Mary\Traits\WithMediaSync;

class Newticket extends Component
{
    use WithFileUploads, WithMediaSync, Toast;

    public bool $newdrawer = false;
    #[Rule("Required")]
    public $issuegroupid;

    #[Rule("Required")]
    public $issuetypeid;

    #[Rule("Required")]
    public $regnumber;

    #[Rule("Required")]
    public $name;

    #[Rule("Required")]
    public $phone;
    #[Rule("Required|email")]
    public $email;
    protected $ictsupportemail="ictsupport@praz.org.zw";
    protected $egpsupportemail="egpsupport@praz.org.zw";

    #[Rule("Required")]
    public $title;
    #[Rule("Required")]
    public $description;

    public array $files = [];
    public Collection $library ;
    public Issuelog $issuelog;
    #[Rule("Sometimes")]
    public  $attachmenttype;

    public array $attachments = [];
    public array $uploadfiles=[];
    protected $customerService;

    public function boot(iservicecustomerInterface $customerService)
    {
        $this->customerService=$customerService;
    }

    public function mount(): void
    {
        $this->library = new Collection();
       if(strtolower(Auth::user()->level)==="bidder"||strtolower(Auth::user()->level)==="entity"){
            $this->regnumber=Auth::user()->user_id;
            $this->name=$this->customerService->getcustomerbyregnumber(Auth::user()->user_id)->name;
        }
        $this->email=Auth::user()->email;
    }

    public function issuegroups(): Collection
    {
        if(strtolower(Auth::user()->level)==="bidder")
        {
            return Issuegroup::where('name','Supplier')->get();
        }elseif(strtolower(Auth::user()->level)==="entity"){
            return Issuegroup::where('name','Procurement Entity')->get();
        }else{
            return Issuegroup::all();
        }
    }

    public function issuetypes(): Collection
    {
        $bidderissuetypes=['tender','registration', 'payment', 'invoice', 'document verification', 'refund', 'general'];
        $entityissuetypes=['app','tender','registration', 'payment', 'invoice', 'refund', 'general'];
        if(strtolower(Auth::user()->level)==="bidder")
        {
            return Issuetype::whereIn('name',$bidderissuetypes)->get();
        }elseif(strtolower(Auth::user()->level)==="entity"){
            return Issuetype::whereIn('name',$entityissuetypes)->get();
        }else{
            return Issuetype::all();
        }
      
    }

    public function SaveRecord()
    {
        $this->validate();
        try {
            $rand = rand(0, 99999);
            $ticketnumber =  "TICKET" . $rand;
            $issue = new Issuelog();
            $issue->Issuegroup_id = $this->issuegroupid;
            $issue->Issuetype_id = $this->issuetypeid;
            $issue->Ticket =$ticketnumber;
            $issue->Regnumber = $this->regnumber;
            $issue->Name = $this->name;
            $issue->Email = $this->email;
            $issue->Phone = $this->phone;
            $issue->Title = $this->title;
            $issue->Description = $this->description;
            $issue->user_id = Auth::user()->id;
            $issue->Status ="PENDING";
            $issue->issuestatus="OPEN";
            $issue->priority="NORMAL";
            $issue->attachmenttype = $this->attachmenttype;

            if($this->attachmenttype=="2") {
                if (count($this->attachments) > 0) {

                    foreach ($this->attachments as $attachment) {
                        $uploadfile = $attachment->store(path: 'attachments');
                        $this->uploadfiles[] = ["url" => $uploadfile];
                }
                    $issue->files = $this->uploadfiles;
                }
            }
            $issue->save();
            if($this->attachmenttype=="1") {
                if (count($this->files) > 0) {

                    $this->syncMedia($issue);
                }
            }
                      
            Notification::route('mail',$this->email)->notify(new NewticketNotification($this->name,$ticketnumber,$this->title));
            Notification::route('mail',$this->ictsupportemail)->notify(new NewticketNotification($this->name,$ticketnumber,$this->title));
            Notification::route('mail',$this->egpsupportemail)->notify(new NewticketNotification($this->name,$ticketnumber,$this->title));
            $this->newdrawer = false;
            $this->reset();
            $this->library = new Collection();
            $this->success("Issue successfully created", "success");
            if(strtolower(Auth::user()->level)==="bidder"){
                $this->redirect(route('bidder.issues.log'));
            }elseif(strtolower(Auth::user()->level)==="entity"){
                $this->redirect(route('entity.issues.log'));
            }else{
                $this->redirect(route('admin.issues.logs'));
            }
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function attachmenttypelist(): array
    {
        return [
            ['id' => '0', 'name' => 'No Attachement'],
            ['id' => '1', 'name' => 'Screenshots'],
            ['id' => '2', 'name' => 'Files']
        ];
    }


  
    public function render()
    {
        return view('livewire.admin.issues.newticket',[
            "groups" => $this->issuegroups(),
            "types" => $this->issuetypes(),
            "attachmenttypelist"=>$this->attachmenttypelist()
        ]);
    }
}
 