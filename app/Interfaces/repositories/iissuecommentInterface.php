<?php

namespace App\Interfaces\repositories;

use App\Models\Issuecomment;
use Illuminate\Support\Collection;

interface iissuecommentInterface
{
    public function getall(): Collection;
    public function findbyid(int $id): ?Issuecomment;
    public function create(array $data): Issuecomment;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getbyissuelog(int $issueLogId): Collection;
} 