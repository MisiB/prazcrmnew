<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use Livewire\Component;

class My extends Component
{
    protected $issueService;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function render()
    {
        return view('livewire.admin.issues.my');
    }
}
