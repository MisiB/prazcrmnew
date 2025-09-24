<?php

namespace App\implementation\repositories;

use App\Interfaces\repositories\iissuetypeInterface;
use App\Models\Issuetype;
use Illuminate\Support\Collection;

class _issuetypeRepository implements iissuetypeInterface
{
    protected $issuetype;

    public function __construct(Issuetype $issuetype)
    {
        $this->issuetype = $issuetype;
    }

    public function getall(): Collection
    {
        return $this->issuetype->all();
    }

    public function findbyid(int $id): ?Issuetype
    {
        return $this->issuetype->find($id);
    }

    public function create(array $data): Issuetype
    {
        return $this->issuetype->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $issuetype = $this->findbyid($id);
        if (!$issuetype) {
            return false;
        }
        return $issuetype->update($data);
    }

    public function delete(int $id): bool
    {
        $issuetype = $this->findbyid($id);
        if (!$issuetype) {
            return false;
        }
        return $issuetype->delete();
    }
} 