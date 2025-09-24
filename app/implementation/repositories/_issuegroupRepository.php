<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\iissuegroupInterface;
use App\Models\Issuegroup;
use Illuminate\Support\Collection;

class _issuegroupRepository implements iissuegroupInterface
{
    protected $issuegroup;

    public function __construct(Issuegroup $issuegroup)
    {
        $this->issuegroup = $issuegroup;
    }

    public function getall(): Collection
    {
        return $this->issuegroup->all();
    }

    public function findbyid(int $id): ?Issuegroup
    {
        return $this->issuegroup->find($id);
    }

    public function create(array $data): Issuegroup
    {
        return $this->issuegroup->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $issuegroup = $this->findbyid($id);
        if (!$issuegroup) {
            return false;
        }
        return $issuegroup->update($data);
    }

    public function delete(int $id): bool
    {
        $issuegroup = $this->findbyid($id);
        if (!$issuegroup) {
            return false;
        }
        return $issuegroup->delete();
    }
}