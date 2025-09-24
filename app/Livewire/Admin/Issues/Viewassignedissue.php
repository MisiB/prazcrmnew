<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use App\Models\Issuecomment;
use App\Models\Issuetask;
use App\Models\User;
use App\Notifications\Issuecomment as NotificationsIssuecomment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Viewassignedissue extends Component
{
    use Toast;
    
    public $regnumber;
    public $name;
    public $phone;
    public $email;
    public $title;
    public $description;
    public $userId;
    public $selectedTab = "comments-tab";
    public array $files = [];
    public Collection $library;
    public $issuelog;
    public bool $drawer = false;
    public $images = [];
    #[Rule("required")]
    public $status;
    #[Rule("required")]
    public $comment;

    public $issuecomment;
    public $taskId;
    public $fileimport;
    public bool $importdrawer = false;
    public array $uploadfiles = [];
    public $file;
    public string $error = "";
    public string $success = "";
    public Issuetask $task;
    public $id;
    protected $issueService;
    public $authenticateduser;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function mount($id)
    {
        $this->authenticateduser = Auth::user();
        $this->id = $id;
        $this->library = new Collection();
        $this->task = new Issuetask();
        $this->ShowIssue();
    }

    public function ShowIssue()
    {
        $this->issuelog = $this->issueService->getissuelogbyticket($this->id);
        
        if ($this->issuelog) {
            // Ensure we have the related task and taskId for updates
            $relatedTask = Issuetask::where('type', 'issue-log')
                ->where('source_id', $this->issuelog->id)
                ->first();
            $this->task = $relatedTask ?? new Issuetask();
            $this->taskId = $relatedTask?->id;

            if ($this->issuelog->library != null) {
                foreach ($this->issuelog->library as $library) {
                    $this->images[] = "storage" . $library["path"];
                }
            } else {
                $this->library = new Collection();
            }

            if ($this->issuelog->files != null) {
                foreach ($this->issuelog->files as $file) {
                    $this->uploadfiles[] = ["url" => "storage/" . $file["url"]];
                }
            }
            
            $this->name = $this->issuelog->name;
            $this->regnumber = $this->issuelog->regnumber;
            $this->email = $this->issuelog->email;
            $this->phone = $this->issuelog->phone;
            $this->title = $this->issuelog->title;
            $this->description = $this->issuelog->description;
        }
    }

    public function Savecomment(){
        $this->validate(["issuecomment"=>'required']);
        $assignee = User::find($this->task->assigned_by);
        $respondent= User::find($this->task->user_id);
   
       
        $icomment = new Issuecomment();
        $icomment->issuelog_id = $this->issuelog->id;
        $icomment->user_id = Auth::user()->id;
        $icomment->comment = $this->issuecomment;
        $icomment->save();
        if($this->authenticateduser->id != $this->task->assigned_by){
            
            if($assignee != null){
                $assignee->notify(new NotificationsIssuecomment($this->issuecomment,$this->issuelog->Ticket,$respondent->name, $respondent->surname));
            } 
            if($respondent != null){
               $respondent->notify(new NotificationsIssuecomment($this->issuecomment,$this->issuelog->Ticket,$respondent->name, $respondent->surname));
            }

        }else{
            if($assignee != null){
                $assignee->notify(new NotificationsIssuecomment($this->issuecomment,$this->issuelog->Ticket,$assignee->name, $assignee->surname));
            } 
            if($respondent != null){
                $respondent->notify(new NotificationsIssuecomment($this->issuecomment,$this->issuelog->Ticket,$assignee->name, $assignee->surname));
            }
        }
        $this->reset("issuecomment");
        $this->success("success","comment successfully saved");
    }

    public function Update()
    {
        $this->validate();
        try {
            if (!$this->taskId && $this->issuelog) {
                $this->taskId = Issuetask::where('type', 'issue-log')
                    ->where('source_id', $this->issuelog->id)
                    ->value('id');
            }

            if (!$this->taskId) {
                $this->error('Related task not found for this ticket', 'error');
                return;
            }

            $task = Issuetask::where("id", "=", $this->taskId)->first();
            $issue = $this->issueService->getissuelogbyid($task->source_id);
            
            if ($this->status == "RESOLVED") {
                $task->status = $this->status;
                $task->comments = $this->comment;
                $task->save();
                
                $result = $this->issueService->addcomment($issue->id, ['comment'=>$task->comments ]);
                if ($result['status'] === 'success') {
                    $response=$this->issueService->updateissuelog($this->id,['status'=>'RESOLVED']);
                    if($response['status']==='success')
                    { 
                        $this->success($response['message'], $response['status']);
                        return $this->redirect(route('admin.issues.logs'));
                    }else{
                        return $this->warning($response['message'], $response['status']);
                    }
                } else {
                    return $this->error($result['message'], "error");
                }
            } else {
                $result = $this->issueService->addComment($issue->id, [
                    'comment' => $this->comment
                ]);
                
                if ($result['status'] === 'success') {
                    $this->success("Comment added successfully", "success");
                    return $this->redirect(route('admin.issues.viewassignedissue',['id'=>$this->id]));
                } else {
                    $this->error($result['message'], "error");
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage(), "error");
        }
    }

    public function statuslist(): array
    {
        return [
            ['id' => 'RESOLVED', 'name' => 'RESOLVED'],
            ['id' => 'PENDING', 'name' => 'PENDING']

        ];
    }    
    public function render()
    {
        return view('livewire.admin.issues.viewassignedissue', [
            "issuelog" => $this->issuelog,
            "statuslist" => $this->statuslist(),
            "breadcrumbs" => [['label' => 'Home', 'link' => route('admin.home')],['label' => "My Assignments"]],
        
        ]);
    }
}
