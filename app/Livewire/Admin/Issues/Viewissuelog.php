<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use App\Models\Issuecomment;
use App\Models\Issuetask;
use App\Models\User;
use App\Notifications\Issuecomment as NotificationsIssuecomment;
use App\Notifications\TaskAssigned;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class Viewissuelog extends Component
{
    use Toast;
    
    public $id;
    public $regnumber;
    public $name;
    public $phone;
    public $email;
    public $title;
    public $description;
    public $issuelog;
    public Collection $library;
    public array $uploadfiles = [];
    public $images = [];
    public $selectedTab = "comments-tab";
    public $issuecomment;
    public $userId;
    public $user;

    protected $issueService;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function mount($id)
    {
        $this->id = $id;
        $this->issuelog = null;
        $this->user = Auth::user();
        $this->ShowIssue();
    }

    public function users(): array
    {
        $users = User::where("level", "=", "User")->get();
        $arr = [];
        foreach ($users as $user) {
            $arr[] = ["id" => $user->id, "name" => $user->name . " " . $user->surname];
        }
        return $arr;
    }

    public function ShowIssue()
    {
        $this->issuelog = $this->issueService->getissuelogbyticket($this->id);
        
        if ($this->issuelog) {
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

    public function removeTask($id){
        $task = Issuetask::where("id","=",$id)->first();
       
        $task->delete();
        $this->issuelog->status="PENDING";
        $this->issuelog->save();
        $this->success("success","Task successfully removed");
        $this->redirect(route('admin.issues.log',['id'=>$this->id]), navigate:true);
    }

    public function SaveRecord()
    {
        $this->validate(["userId" => "required"]);
        try {
            $check = Issuetask::where("source_id", "=", $this->issuelog->id)->where("type", "=", "Issue-log")->first();
            if ($check != null) {
                $this->error("Task already assigned to user ", "error");
            } else {
                $task = new Issuetask();
                $task->type = "Issue-log";
                $task->source_id = $this->issuelog->id;
                $task->user_id = $this->userId;
                $task->assigned_by = Auth::user()->id;
                $task->status = "PENDING";
                $task->save();

                $result = $this->issueService->updateissuelog($this->issuelog->ticket, ['status' => 'ASSIGNED']);
                
                if ($result['status'] === 'success') {
                    $user = User::where("id", "=", $this->userId)->first();
                    Notification::send($user, new TaskAssigned($user->name, $user->surname, $task->id));
                    $this->success("Task successfully assigned to user", "success");
                } else {
                    $this->error($result['message'], "error");
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage(), "error");
        }
    }

    public function Savecomment(){
        $this->validate(["issuecomment"=>'required']);

        try {
            $assignee = User::where("id","=",$this->issuelog->task->user_id)->first();
            $icomment = new Issuecomment();
            $icomment->issuelog_id = $this->issuelog->id;
            $icomment->user_id = Auth::user()->id;
            $icomment->comment = $this->issuecomment;
            $icomment->save();
            Notification::send($assignee, new NotificationsIssuecomment($assignee->name, $assignee->surname, $this->issuelog->ticket,$this->issuecomment));
            $this->reset("issuecomment");
            return $this->success("success","comment successfully saved");
        } catch (\Exception $e) {
            return $this->toast("warning","Please assign ticket first before adding comment");
        }

    }

    public function delete($id)
    {
        try {
            $menu = Issuetask::Where("id", "=", $id)->where("type", "=", "Issue-log")->first();
            $menu->delete();
            $this->success("Task successfully deleted", "success");
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function CloseIssue($id)
    {
        try {
            $issue = $this->issueService->getissuelogbyticket($id);
            $result = $this->issueService->updateissuelog($id, ['issuestatus' => 'CLOSED']);
            
            if ($result['status'] === 'success') {
                $this->success("Issue has been closed", "success");
            } else {
                $this->warning($result['message'], "error");
            }
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }
    public function render()
    {
        return view('livewire.admin.issues.viewissuelog', [
            "issuelog" => $this->issuelog,
            "users" => $this->users()
        ]);
    }
}
