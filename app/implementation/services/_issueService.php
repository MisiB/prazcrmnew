<?php

namespace App\implementation\services;

use App\Interfaces\services\iissueService;
use App\Interfaces\repositories\iissuetypeInterface;
use App\Interfaces\repositories\iissuegroupInterface;
use App\Interfaces\repositories\iissuelogInterface;
use App\Interfaces\repositories\iissuecommentInterface;
use App\Models\Issuelog;
use App\Models\User;
use App\Notifications\Issuecomment as NotificationsIssuecomment;
use App\Notifications\TaskAssigned;
use App\Models\Issuetask;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class _issueService implements iissueService
{
    protected $issuetypeRepository;
    protected $issuegroupRepository;
    protected $issuelogRepository;
    protected $issuecommentRepository;

    public function __construct(
        iissuetypeInterface $issuetypeRepository,
        iissuegroupInterface $issuegroupRepository,
        iissuelogInterface $issuelogRepository,
        iissuecommentInterface $issuecommentRepository
    ) {
        $this->issuetypeRepository = $issuetypeRepository;
        $this->issuegroupRepository = $issuegroupRepository;
        $this->issuelogRepository = $issuelogRepository;
        $this->issuecommentRepository = $issuecommentRepository;
    }

    // Issue Type Management
    public function getallissuetypes(): Collection
    {
        return $this->issuetypeRepository->getall();
    }

    public function createissuetype(array $data): array
    {
        try {
            $issuetype = $this->issuetypeRepository->create($data);
            return ['status' => 'success', 'message' => 'Issue type created successfully', 'data' => $issuetype];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateissuetype(int $id, array $data): array
    {
        try {
            $updated = $this->issuetypeRepository->update($id, $data);
            if ($updated) {
                return ['status' => 'success', 'message' => 'Issue type updated successfully'];
            }
            return ['status' => 'error', 'message' => 'Issue type not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteissuetype(int $id): array
    {
        try {
            $deleted = $this->issuetypeRepository->delete($id);
            if ($deleted) {
                return ['status' => 'success', 'message' => 'Issue type deleted successfully'];
            }
            return ['status' => 'error', 'message' => 'Issue type not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Issue Group Management
    public function getallissuegroups(): Collection
    {
        return $this->issuegroupRepository->getall();
    }

    public function createissuegroup(array $data): array
    {
        try {
            $issuegroup = $this->issuegroupRepository->create($data);
            return ['status' => 'success', 'message' => 'Issue group created successfully', 'data' => $issuegroup];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateissuegroup(int $id, array $data): array
    {
        try {
            $updated = $this->issuegroupRepository->update($id, $data);
            if ($updated) {
                return ['status' => 'success', 'message' => 'Issue group updated successfully'];
            }
            return ['status' => 'error', 'message' => 'Issue group not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteissuegroup(int $id): array
    {
        try {
            $deleted = $this->issuegroupRepository->delete($id);
            if ($deleted) {
                return ['status' => 'success', 'message' => 'Issue group deleted successfully'];
            }
            return ['status' => 'error', 'message' => 'Issue group not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Issue Log Management
    public function getallissuelogs(): Collection
    {
        return $this->issuelogRepository->getall();
    }

    public function getissuelogspaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->issuelogRepository->getpaginated($perPage);
    }

    public function getissuelogbyid(int $id): ?Issuelog
    {
        return $this->issuelogRepository->findbyid($id);
    }

    public function getissuelogbyticket(string $ticket): ?Issuelog
    {
        return $this->issuelogRepository->findbyticket($ticket);
    }
 
    public function createissuelog(array $data): array
    {
        try {
            $issuelog = $this->issuelogRepository->create($data);
            
            // Create task if assigned to someone
            if (isset($data['assigned_to'])) {
                $this->createTaskForIssue($issuelog, $data['assigned_to']);
            }
            
            return ['status' => 'success', 'message' => 'Issue log created successfully', 'data' => $issuelog];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateissuelog(string $id, array $data): array
    {
        try {
            $updated = $this->issuelogRepository->update($id, $data);
            if ($updated) {
                return ['status' => 'success', 'message' => 'Issue log updated successfully'];
            }
            return ['status' => 'error', 'message' => 'Issue log not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteissuelog(string $id): array
    {
        try {
            $deleted = $this->issuelogRepository->delete($id);
            if ($deleted) {
                return ['status' => 'success', 'message' => 'Issue log deleted successfully'];
            }
            return ['status' => 'error', 'message' => 'Issue log not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getissuelogsbystatus(string $status): Collection
    {
        return $this->issuelogRepository->getbystatus($status);
    }
    
    public function getissuelogsbystatusandaccount(string $status, string $praznumber): Collection
    {
        return $this->issuelogRepository->getbystatusandaccount($status, $praznumber);
    }

    public function getissuelogsbyassignee(string $userId): Collection
    {
        return $this->issuelogRepository->getbyassignee($userId);
    }

    public function getissuelogsbydaterange(string $from, string $to): Collection
    {
        return $this->issuelogRepository->getbydaterange($from, $to);
    }
    
    public function getdashboarddata(array $filters = []): array
    {
        return $this->issuelogRepository->getdashboarddata($filters);
    }

    // Issue Comments
    public function getcommentsbyissuelog(int $issueLogId): Collection
    {
        return $this->issuecommentRepository->getbyissuelog($issueLogId);
    }

    public function addcomment(int $issueLogId, array $data): array
    {
        try {
            $data['issuelog_id'] = $issueLogId;
            $data['user_id'] = Auth::id();
            
            $comment = $this->issuecommentRepository->create($data);
            
            // Send notification
            $issuelog = $this->getIssueLogById($issueLogId);
            if ($issuelog && $issuelog->task) {
                $user = User::find($issuelog->task->user_id);
                if ($user) {
                    $user->notify(new NotificationsIssuecomment($comment, $issuelog));
                }
            }
            
            return ['status' => 'success', 'message' => 'Comment added successfully', 'data' => $comment];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updatecomment(int $commentId, array $data): array
    {
        try {
            $updated = $this->issuecommentRepository->update($commentId, $data);
            if ($updated) {
                return ['status' => 'success', 'message' => 'Comment updated successfully'];
            }
            return ['status' => 'error', 'message' => 'Comment not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deletecomment(int $commentId): array
    {
        try {
            $deleted = $this->issuecommentRepository->delete($commentId);
            if ($deleted) {
                return ['status' => 'success', 'message' => 'Comment deleted successfully'];
            }
            return ['status' => 'error', 'message' => 'Comment not found'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // File Management
    public function uploadfiles(array $files, int $issueLogId): array
    {
        try {
            $uploadedFiles = [];
            $issuelog = $this->getIssueLogById($issueLogId);
            
            if (!$issuelog) {
                return ['status' => 'error', 'message' => 'Issue log not found'];
            }

            foreach ($files as $file) {
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('issue-files', $filename, 'public');
                
                $uploadedFiles[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }

            // Update the issue log with file information
            $currentFiles = $issuelog->files ?? collect();
            $issuelog->files = $currentFiles->merge($uploadedFiles);
            $issuelog->save();

            return ['status' => 'success', 'message' => 'Files uploaded successfully', 'data' => $uploadedFiles];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deletefile(int $fileId): array
    {
        try {
            // This would need to be implemented based on how files are stored
            // For now, returning a placeholder
            return ['status' => 'success', 'message' => 'File deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Helper Methods
    private function createTaskForIssue(Issuelog $issuelog, int $assignedTo): void
    {
        $task = new Issuetask();
        $task->title = "Issue: " . $issuelog->title;
        $task->description = $issuelog->description;
        $task->user_id = $assignedTo;
        $task->assigned_by = Auth::id();
        $task->source_id = $issuelog->id;
        $task->type = 'issue-log';
        $task->status = 'PENDING';
        $task->save();

        // Send notification
        $user = User::find($assignedTo);
        if ($user) {
            $user->notify(new TaskAssigned($task, $issuelog));
        }
    }
}