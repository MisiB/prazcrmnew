<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Issuetypes extends Component
{
    use Toast;

    #[Rule('Required')]
    public string $name;
    public bool $newdrawer = false;
    public bool $editdrawer = false;
    public $type;

    protected $issueService;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function issuetypes(): Collection
    {
        return $this->issueService->getallissuetypes();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name']
        ];
    }

    public function Save()
    {
        try {
            $this->validate();
            $result = $this->issueService->createIssueType(['name' => $this->name]);
            
            if ($result['status'] === 'success') {
                $this->reset();
                $this->success($result['message'], "success");
            } else {
                $this->warning($result['message'], "error");
            }
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function edit($id)
    {
        $this->type = $this->issueService->getallissuetypes()->where('id', $id)->first();
        $this->name = $this->type->name;
        $this->editdrawer = true;
    }

    public function Update()
    {
        try {
            $this->validate();
            $result = $this->issueService->updateissuetype($this->type->id, ['name' => $this->name]);
            
            if ($result['status'] === 'success') {
                $this->reset();
                $this->success($result['message'], "success");
            } else {
                $this->warning($result['message'], "error");
            }
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function delete($id)
    {
        try {
            $result = $this->issueService->deleteissuetype($id);
            
            if ($result['status'] === 'success') {
                $this->success($result['message'], "success");
            } else {
                $this->warning($result['message'], "error");
            }
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function render()
    {
        return view('livewire.admin.issues.issuetypes', [
            'types' => $this->issuetypes(),
            'headers' => $this->headers()
        ]);
    }

    public function clear()
    {
        $this->reset(['name']);
        $this->newdrawer = false;
        $this->editdrawer = false;
    }
}
