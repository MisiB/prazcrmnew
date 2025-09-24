<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use Livewire\Component;

class Configuration extends Component
{
    protected $issueService;
    public string $selectedTab ="issuegroup-tab";
    
    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function render()
    {
        return view('livewire.admin.issues.configuration',[
            "breadcrumbs" => [['label' => 'Home', 'link' => route('admin.home')],['label' => "Configurations"]],
        ]);
    }
}
   