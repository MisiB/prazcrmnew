<?php

namespace App\Interfaces\repositories;

use App\Models\Issuegroup;
use Illuminate\Support\Collection;

interface iissuegroupInterface
{
    public function getall(): Collection;
    public function findbyid(int $id): ?Issuegroup;
    public function create(array $data): Issuegroup;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
} 