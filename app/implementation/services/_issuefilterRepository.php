<?php

namespace App\implementation\services;

use App\Interfaces\services\iissuefilterService;

class _issuefilterRepository extends _apifilterRepositiory implements iissuefilterService
{
    protected $safeparams = [
        'regnumber' => ['eq'],
        'email'=> ['eq']
    ];
    protected $columnmap = [
    ];
}
