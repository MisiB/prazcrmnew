<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\iissuelogInterface;
use App\Models\Issuelog;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class _issuelogRepository implements iissuelogInterface
{
    protected $issuelog;

    public function __construct(Issuelog $issuelog)
    {
        $this->issuelog = $issuelog;
    }

    public function getall(): Collection
    {
        return $this->issuelog->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])->get();
    }

    public function findbyid(int $id): ?Issuelog
    {
        return $this->issuelog->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])->find($id);
    }

    public function findbyticket(string $ticket): ?Issuelog
    {
        return $this->issuelog
            ->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])
            ->where('Ticket', $ticket)
            ->first();
    }

    public function create(array $data): Issuelog
    {
        return $this->issuelog->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $issuelog = $this->findbyticket($id);
        if (!$issuelog) {
            return false;
        }
        return $issuelog->update($data);
    }

    public function delete(string $id): bool
    {
        $issuelog = $this->findbyticket($id);
        if (!$issuelog) {
            return false;
        }
        return $issuelog->delete();
    }

    public function getpaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->issuelog->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])
            ->paginate($perPage);
    }

    public function getbystatus(string $status): Collection
    {
        return $this->issuelog->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])
            ->where('status', $status)
            ->get();
    }
    public function getbystatusandaccount($status, $praznumber): Collection
    {
        return $this->issuelog->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])
            ->where('status', $status)
            ->where('regnumber', $praznumber)
            ->get();
    }
    public function getbyassignee(string $userId): Collection
    {
        return $this->issuelog->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])
            ->whereHas('task', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
    }

    public function getbydaterange(string $from, string $to): Collection
    {
        return $this->issuelog->with(['issuetype', 'issuegroup', 'comments.user', 'task.user'])
            ->whereBetween('created_at', [$from, $to])
            ->get();
    }

    public function getdashboarddata(array $filters = []): array
    {
        $query = $this->issuelog->with(['issuetype', 'issuegroup', 'task.user']);

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
        }

        if (isset($filters['issuetype_id'])) {
            $query->where('Issuetype_id', $filters['issuetype_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $issues = $query->get();

        return [
            'total_issues' => $issues->count(),
            'pending_issues' => $issues->where('status', 'pending')->count(),
            'resolved_issues' => $issues->where('status', 'resolved')->count(),
            'in_progress_issues' => $issues->where('status', 'in_progress')->count(),
            'issues_by_type' => $issues->groupBy('issuetype.name'),
            'issues_by_group' => $issues->groupBy('issuegroup.name'),
            'issues_by_assignee' => $issues->groupBy('task.user.name'),
            'issues' => $issues
        ];
    }
}