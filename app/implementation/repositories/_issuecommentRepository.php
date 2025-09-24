<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\iissuecommentInterface;
use App\Models\Issuecomment;
use Illuminate\Support\Collection;

class _issuecommentRepository implements iissuecommentInterface
{
    protected $issuecomment;

    public function __construct(Issuecomment $issuecomment)
    {
        $this->issuecomment = $issuecomment;
    }

    public function getall(): Collection
    {
        return $this->issuecomment->with('user')->get();
    }

    public function findbyid(int $id): ?Issuecomment
    {
        return $this->issuecomment->with('user')->find($id);
    }

    public function create(array $data): Issuecomment
    {
        return $this->issuecomment->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $issuecomment = $this->findbyid($id);
        if (!$issuecomment) {
            return false;
        }
        return $issuecomment->update($data);
    }

    public function delete(int $id): bool
    {
        $issuecomment = $this->findbyid($id);
        if (!$issuecomment) {
            return false;
        }
        return $issuecomment->delete();
    }

    public function getbyissuelog(int $issueLogId): Collection
    {
        return $this->issuecomment->with('user')
            ->where('issuelog_id', $issueLogId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
} 