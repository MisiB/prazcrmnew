<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class Issuegroups extends Component
{
    use Toast;

    #[Rule('Required')]
    public string $name;
    public bool $newdrawer = false;
    public bool $editdrawer = false;
    public $group;

    protected $issueService;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function issuegroups(): Collection
    {
        return $this->issueService->getallissuegroups();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name']
        ];
    }

    public function save()
    {
        try {
            $this->validate();
            $result = $this->issueService->createissuegroup(['name' => $this->name]);
            
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
        $this->group = $this->issueService->getallissuegroups()->where('id', $id)->first();
        $this->name = $this->group->name;
        $this->editdrawer = true;
    }

    public function update()
    {
        try {
            $this->validate();
            $result = $this->issueService->updateissuelog($this->group->id, ['name' => $this->name]);
            
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
            $result = $this->issueService->deleteissuegroup($id);
            
            if ($result['status'] === 'success') {
                $this->success($result['message'], "success");
            } else {
                $this->warning($result['message'], "error");
            }
        } catch (\Exception $e) {
            $this->warning($e->getMessage(), "error");
        }
    }

    public function clear()
    {
        $this->reset(['name']);
        $this->newdrawer = false;
        $this->editdrawer = false;
    }

    public function render()
    {
        return view('livewire.admin.issues.issuegroups', [
            'groups' => $this->issuegroups(),
            'headers' => $this->headers()
        ]);
    }
}
