<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use App\Models\Issuetask;
use App\Models\Issuetype;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Mary\Traits\WithMediaSync;
use PHPUnit\Runner\Baseline\Issue;

class Assignedissues extends Component
{
    use WithFileUploads, WithMediaSync, Toast;

    public $regnumber;
    public $name;
    public $phone;
    public $email;
    public $title;
    public $description;
    public $userId;
    public array $files = [];
    public Collection $library;
    public $issuelog;
    public bool $drawer = false;
    public $images = [];
    #[Rule("required")]
    public $status;
    #[Rule("required")]
    public $comment;
    public $taskId;
    public $fileimport;
    public bool $importdrawer = false;
    public array $uploadfiles = [];
    public $file;
    public string $error = "";
    public string $success = "";
    public Issuetask $task;
    public Issuetype $issuetype;

    protected $issueService;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function mount()
    {
        $this->library = new Collection();
        $this->task = new Issuetask();
        $this->issuetype= new Issuetype();
    }
  
    public function tasks(): Collection
    {
        return $this->issueService->getissuelogsbyassignee(Auth::user()->id)
            ->map(function ($issue) {
                return $issue->task;
            })
            ->filter()
            ->where('status', 'PENDING');
    }

    public function headers()
    {
        return [
            ['key' => 'issuetype', 'label' => 'Issue type'],
            ['key' => 'Ticket', 'label' => 'Ticket Number'],
            ['key' => 'priority', 'label' => 'Priority'],
            ['key' => 'Name', 'label' => 'Organisation'],
            ['key' => 'Title', 'label' => 'Title'],
            ['key' => 'Status', 'label' => 'Ticket Status'],
            ['key' => 'Issuestatus', 'label' => 'Issue Status'],
            ['key' => 'created_at', 'label' => 'Created At'],
        ];
    }

    public function statuslist(): array
    {
        return [
            ['id' => '', 'name' => ''],
            ['id' => 'RESOLVED', 'name' => 'RESOLVED'],
            ['id' => 'PENDING', 'name' => 'PENDING']
        ];
    }

    public function with(): array
    {
        return [
            "headers" => $this->headers(),
            "tasks" => $this->tasks(),
            "statuslist" => $this->statuslist()
        ];
    }

    public function showissue($id)
    {
        $this->reset("images");
        $this->taskId = $id;
        $task = Issuetask::with('issuelog.comments.user')->where("id", "=", $id)->first();
        $this->issuelog = $task->issuelog;
        
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
        $this->task = $task;
        $this->drawer = true;
    }

    public function update()
    {
        $this->validate();
        try {
            $task = Issuetask::where("id", "=", $this->taskId)->first();
            $issue = $this->issueService->getissuelogbyid($task->source_id);
            
            if ($this->status == "RESOLVED") {
                $task->status = $this->status;
                $task->Comments = $this->comment;
                $task->save();
                
                $result = $this->issueService->updateissuelog($issue->id, ['Status' => $this->status]);
                if ($result['status'] === 'success') {
                    $this->success("Task successfully completed", "success");
                } else {
                    $this->error($result['message'], "error");
                }
            } else {
                $result = $this->issueService->addcomment($issue->id, [
                    'comment' => $this->comment
                ]);
                
                if ($result['status'] === 'success') {
                    $this->success("Comment added successfully", "success");
                } else {
                    $this->error($result['message'], "error");
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage(), "error");
        }
    }

    public function export()
    {
        $csvfilename = "Myissues.csv";
        $file = fopen($csvfilename, "w");
        $tasks = $this->tasks();
        $array[] = ["TaskId" => "TaskId", "Ticketnumber" => "Ticket number", "Organisation" => "Organisation", "Email" => "Email", "Phone" => "Phone", "Regnumber" => "Regnumber", "Title" => "Title", "Description" => "Description", "Type" => "Type", "Group" => "Group", "Images" => "Images", "Attachments" => "Attachments", "CreatedAt" => "Date Created", "Status" => "Status", "Comment" => "Comment"];
        
        foreach ($tasks as $key => $task) {
            $files = $task->issuelog->library?->pluck('url');
            $urls = "";

            if ($task->issuelog->library != null) {
                if (count($files) > 0) {
                    foreach ($files as $key => $value) {
                        $position = strpos($value, '?');
                        $result = substr($value, 0, $position);
                        $urls = $urls . " " . str_replace("\\", "/", $result);
                    }
                }
            }
            
            $attachments = $task->issuelog->files?->pluck('url');
            $attachmenturl = "";

            if ($task->issuelog->files != null) {
                if ($attachments != null) {
                    foreach ($attachments as $key => $value) {
                        $path = url($value);
                        $attachmenturl = $attachmenturl . " " . $path;
                    }
                }
            }
            
            $array[] = [
                "TaskId" => $task->id,
                "Ticketnumber" => $task->issuelog->ticket,
                "Organisation" => $task->issuelog->name,
                "Email" => $task->issuelog->email,
                "Phone" => $task->issuelog->phone,
                "Regnumber" => $task->issuelog->regnumber,
                "Title" => $task->issuelog->title,
                "Description" => $task->issuelog->description,
                "Type" => $task->issuelog->issuetype->name,
                "Group" => $task->issuelog->issuegroup->name,
                "Images" => $urls,
                "Attachments" => $attachmenturl,
                "CreatedAt" => $task->created_at,
                "Status" => "",
                "Comment" => ""
            ];
        }

        foreach ($array as $task) {
            fputcsv($file, $task);
        }

        fclose($file);
        return response()->download(public_path($csvfilename))->deleteFileAfterSend(true);
    }

    public function import()
    {
        try {
            $filename = Str::random() . ".csv";
            $path = $this->file->storeAs(path: 'issuelogs', name: $filename);
            $file = fopen(storage_path('app/private/issuelogs/' . $filename), 'r');
            $errors[] = ["TaskId" => "TaskId", "Comment" => "Comment"];
            $uploaded = 0;
            $i = 0;
            
            while (($row = fgetcsv($file, null, ',')) != false) {
                if ($i > 0) {
                    $id = $row[0];
                    $status = $row[13];
                    $comment = $row[14];
                    $task = Issuetask::where("id", "=", $id)->first();
                    
                    if ($task != null) {
                        $issue = $this->issueService->getissuelogbyid($task->source_id);
                        
                        if (strtoupper($status) == "R") {
                            $task->Status = "RESOLVED";
                            $task->Comments = $comment;
                            $task->save();

                            if ($issue != null) {
                                $this->issueService->updateissuelog($issue->id, ['Status' => 'RESOLVED']);
                            }
                            $uploaded = $uploaded + 1;
                        } else if (strtoupper($status) == "P") {
                            $this->issueService->addcomment($issue->id, ['comment' => $comment]);
                        } else {
                            $errors[] = ["TaskId" => $id, "Comment" => "TaskId not updated because the follow status was not found: " . $status];
                        }
                    } else {
                        $errors[] = ["TaskId" => $id, "Comment" => "TaskId not found"];
                    }
                }
                $i++;
            }
            
            if ($uploaded > 0) {
                $this->success = $uploaded . " records successfully uploaded";
            }
            
            if (count($errors) > 1) {
                $this->error = count($errors) . " errors found on uploaded file";
                $csvfilename = "uploaderrors.csv";
                $file = fopen($csvfilename, "w");
                foreach ($errors as $err) {
                    fputcsv($file, $err);
                }
                fclose($file);
                return response()->download(public_path($csvfilename))->deleteFileAfterSend(true);
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.issues.assignedissues', [
            "headers" => $this->headers(),
            "tasks" => $this->tasks(),
            "statuslist" => $this->statuslist(),
            "task" => $this->task,  
            "breadcrumbs" => [['label' => 'Home', 'link' => route('admin.home')],['label' => "Assigned issues"]],
     
        ]);
    }
}
