<?php

namespace App\Interfaces\repositories;

use App\Models\Issuelog;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface iissuelogInterface
{
    public function getall(): Collection;
    public function findbyid(int $id): ?Issuelog;
    public function findbyticket(string $ticket): ?Issuelog;
    public function create(array $data): Issuelog;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function getpaginated(int $perPage = 15): LengthAwarePaginator;
    public function getbystatus(string $status): Collection;
    public function getbystatusandaccount(string $status, string $praznumber): Collection;
    public function getbyassignee(string $userId): Collection;
    public function getbydaterange(string $from, string $to): Collection;
    public function getdashboarddata(array $filters = []): array;
} 