<?php

namespace App\Interfaces\repositories;

use App\Models\Issuetype;
use Illuminate\Support\Collection;

interface iissuetypeInterface
{
    public function getall(): Collection;
    public function findbyid(int $id): ?Issuetype;
    public function create(array $data): Issuetype;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
