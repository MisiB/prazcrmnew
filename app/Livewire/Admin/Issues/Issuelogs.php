<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use App\Models\Issuetype;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TicketclosedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Mary\Traits\WithMediaSync;

class Issuelogs extends Component
{
    use WithPagination, WithFileUploads, WithMediaSync, Toast;

    public $issuelogs;
    public $search;
    public $regnumber;
    public $name;
    public $phone;
    public $email;
    public $title;
    public $description;
    public $userid;
    public array $files = [];
    public Collection $library;
    public $issuelog;
    public bool $drawer = false;
    public $images = [];
    public $selectedTab = "pending-tab";

    #[Rule("required")]
    public $status;
    #[Rule("required")]
    public $comment;

    protected $issueService;
    public Issuetype $issuetype;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function mount()
    {
        $this->library = new Collection();
        $this->issuetype= new Issuetype();
    }

    public function logs(): Collection
    {
        return $this->issueService->getissuelogsbystatus('PENDING')
            ->where('issuestatus', 'OPEN')
            ->filter(function ($issue) {
                if (empty($this->search)) return true;
                return str_contains(strtolower($issue->name), strtolower($this->search)) ||
                       str_contains(strtolower($issue->ticket), strtolower($this->search))||
                       str_contains(strtolower($issue->title), strtolower($this->search))||
                       str_contains(strtolower($issue->description), strtolower($this->search))||
                       str_contains(strtolower($issue->regnumber), strtolower($this->search))||
                       str_contains(strtolower($issue->issuetype->name), strtolower($this->search));
            });

    }

    public function assignedlogs(): Collection
    {
        return $this->issueService->getissuelogsbystatus('ASSIGNED')
            ->where('issuestatus', 'OPEN')
            ->filter(function ($issue) {
                if (empty($this->search)) return true;
                return str_contains(strtolower($issue->name), strtolower($this->search)) ||
                       str_contains(strtolower($issue->ticket), strtolower($this->search))||
                       str_contains(strtolower($issue->title), strtolower($this->search))||
                       str_contains(strtolower($issue->description), strtolower($this->search))||
                       str_contains(strtolower($issue->regnumber), strtolower($this->search))||
                       str_contains(strtolower($issue->issuetype->name), strtolower($this->search));
            });
    }

    public function resolvedlogs(): Collection
    { 
        return $this->issueService->getissuelogsbystatus('RESOLVED')
            ->where('issuestatus', 'OPEN')
            ->filter(function ($issue) {
                if (empty($this->search)) return true;
                return str_contains(strtolower($issue->name), strtolower($this->search)) ||
                       str_contains(strtolower($issue->ticket), strtolower($this->search))||
                       str_contains(strtolower($issue->title), strtolower($this->search))||
                       str_contains(strtolower($issue->description), strtolower($this->search))||
                       str_contains(strtolower($issue->regnumber), strtolower($this->search))||
                       str_contains(strtolower($issue->issuetype->name), strtolower($this->search));
            });
    }

    public function closedlogs(): Collection
    {
        return $this->issueService->getissuelogsbystatus('RESOLVED')
            ->where('issuestatus', 'CLOSED')
            ->filter(function ($issue) {
                if (empty($this->search)) return true;
                return str_contains(strtolower($issue->name), strtolower($this->search)) ||
                       str_contains(strtolower($issue->ticket), strtolower($this->search))||
                       str_contains(strtolower($issue->title), strtolower($this->search))||
                       str_contains(strtolower($issue->description), strtolower($this->search))||
                       str_contains(strtolower($issue->regnumber), strtolower($this->search))||
                       str_contains(strtolower($issue->issuetype->name), strtolower($this->search));
            });
    }

    public function headers()
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'Ticket', 'label' => 'Ticket Number'],
            ['key' => 'Name', 'label' => 'Organisation'],
            ['key' => 'Title', 'label' => 'Title'],
            ['key' => 'Status', 'label' => 'Status'],
            ['key' => 'Assigned', 'label' => 'Assigned To'],
            ['key' => 'created_at', 'label' => 'Created At'],
        ];
    }

    public function users(): array
    {
        $users = User::all();
        $arr = ["id" => "", "name" => ""];
        foreach ($users as $user) {
            $arr[] = ["id" => $user->id, "name" => $user->name . " " . $user->surname];
        }
        return $arr;
    }

    public function showissue($id)
    {
        $this->reset("images");
        $this->issuelog = $this->issueService->getissuelogbyticket($id);
        
        if ($this->issuelog->library != null) {
            foreach ($this->issuelog->library as $library) {
                $this->images[] = "storage" . $library["path"];
            }
        } else {
            $this->library = new Collection();
        }
        
        $this->name = $this->issuelog->name;
        $this->regnumber = $this->issuelog->regnumber;
        $this->email = $this->issuelog->email;
        $this->phone = $this->issuelog->phone;
        $this->title = $this->issuelog->title;
        $this->description = $this->issuelog->description;
        $this->drawer = true;
    }

    public function saverecord()
    {
        $this->validate(["userId" => "required"]);
        try {
            $check = Task::where("source_id", "=", $this->issuelog->id)->where("type", "=", "Issue-log")->first();
            if ($check != null) {
                $this->error("Task already assigned to user ", "error");
            } else {
                $task = new Task();
                $task->uuid=$this->issuelog->ticket;
                $task->user_id = $this->userid;
                $task->source_id = $this->issuelog->id;
                $task->type = "Issue-log";
                $task->assigned_by = Auth::user()->id;
                $task->status = "PENDING";
                $task->save();

                $result = $this->issueService->updateissuelog($this->issuelog->id, ['Status' => 'ASSIGNED']);
                
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

    public function delete($id)
    {
        try {
            $menu = Task::Where("id", "=", $id)->where("type", "=", "Issue-log")->first();
            $menu->delete();
            $this->success("Task successfully deleted", "success");
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function closeissue($id)
    {
        try {
            $issue = $this->issueService->getissuelogbyticket($id);
            $result = $this->issueService->updateissuelog($id, ['issuestatus' => 'CLOSED']);
            
            if ($result['status'] === 'success') {
                $this->success("Issue has been closed", "success");
                Notification::route("mail", $issue->Email)
                    ->notify(new TicketclosedNotification($issue->Name, $issue->Regnumber, $issue->Ticket, $issue->task->Comments));
            } else {
                $this->warning($result['message'], "error");
            }
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function render()
    {
        return view('livewire.admin.issues.issuelogs', [
            "headers" => $this->headers(),
            "logs" => $this->logs(),
            "assignedlogs" => $this->assignedlogs(),
            "resolvedlogs" => $this->resolvedlogs(),
            "closedlogs" => $this->closedlogs(),
            "users" => $this->users(),
            "breadcrumbs" => [['label' => 'Home', 'link' => route('admin.home')],['label' => "Logs"]],
        ]);
    }
}
