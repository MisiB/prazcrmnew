<?php

namespace App\Interfaces\services;

use App\Models\Issuelog;
use App\Models\Issuetype;
use App\Models\Issuegroup;
use App\Models\Issuecomment;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface iissueService
{
    // Issue Type Management
    public function getallissuetypes(): Collection;
    public function createissuetype(array $data): array;
    public function updateissuetype(int $id, array $data): array;
    public function deleteissuetype(int $id): array;

    // Issue Group Management
    public function getallissuegroups(): Collection;
    public function createissuegroup(array $data): array;
    public function updateissuegroup(int $id, array $data): array;
    public function deleteissuegroup(int $id): array;
    
    // Issue Log Management
    public function getallissuelogs(): Collection;
    public function getissuelogspaginated(int $perPage = 15): LengthAwarePaginator;
    public function getissuelogbyid(int $id): ?Issuelog;
    public function getissuelogbyticket(string $ticket): ?Issuelog;
    public function createissuelog(array $data): array;
    public function updateissuelog(string $id, array $data): array;
    public function deleteissuelog(string $id): array;
    public function getissuelogsbystatus(string $status): Collection;
    public function getissuelogsbystatusandaccount(string $status, string $praznumber): Collection;
    public function getissuelogsbyassignee(string $userId): Collection;
    public function getissuelogsbydaterange(string $from, string $to): Collection;
    public function getdashboarddata(array $filters = []): array;

    // Issue Comments
    public function getcommentsbyissuelog(int $issueLogId): Collection;
    public function addcomment(int $issueLogId, array $data): array;
    public function updatecomment(int $commentId, array $data): array;
    public function deletecomment(int $commentId): array;
    
    // File Management
    public function uploadfiles(array $files, int $issueLogId): array;
    public function deletefile(int $fileId): array;
}