<div>
    <x-breadcrumbs :items="$breadcrumbs" 
    class="bg-base-300 p-3 mt-2 rounded-box"
    link-item-class="text-sm font-bold" />



<x-card title="{{ $strategy->name }}" subtitle="{{ $strategy->status }}" separator class="mt-5 border-2 border-gray-200">
    <x-slot:menu>
        <x-button icon="o-plus" label="Add Programme" wire:click="openModal()" class="btn-outline" />
    </x-slot:menu>
    <table class="table table-xs table-zebra">
        <thead>
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($strategy?->programmes??[] as $prog)
            <tr>
                <td>{{ $prog->code }}</td>
                <td>{{ $prog->title }}</td>
                <td class ="flex justify-end gap-2">
                    <x-button icon="o-magnifying-glass" label="View " class="btn-success  btn-sm" wire:click="openViewModal({{ $prog->id }})" />              
                    <x-button icon="o-pencil" label="Edit" class="btn-info  btn-sm" wire:click="getprogramme({{ $prog->id }})" />
                    <x-button icon="o-trash" label="Delete" class="btn-error  btn-sm" wire:click="deleteprogramme({{ $prog->id }})" wire:confirm="Are you sure you want to delete this programme?" />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">
                    <x-alert class="alert-error" icon="o-exclamation-triangle" title="No programmes found" />
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</x-card>
<x-modal title="{{ $id ? 'Edit' : 'Add' }} Programme" wire:model="modal">
    <x-form wire:submit="save">
        <div class="grid gap-2">
            <x-input label="Code" wire:model="code" />
            <x-input label="Title" wire:model="title" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModal()" />
            <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>

<x-modal title="{{ $programme->title ?? 'View Programme' }}" wire:model="viewModal" box-class="max-w-6xl h-screen" separator>
   
    <div class="flex justify-between">
        <x-input placeholder="Search" wire:model="search" class="w-1/2" />
    <x-button icon="o-plus" class="btn btn-primary" label="Add Outcome" wire:click="outcomemodal=true" />
    </div>
    <x-hr/>
   
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>Outcome</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($programme?->outcomes??[] as $outcome)
            <tr>
                <td>{{ $outcome->title }}</td>
                <td class="flex justify-end gap-2">
                    <x-button icon="o-magnifying-glass" label="indicators" class="btn-success b btn-sm" wire:click="getinidicators({{ $outcome->id }})"/>
              
                    <x-button icon="o-pencil" label="Edit" class="btn-info  btn-sm" wire:click="editoutcome({{ $outcome->id }})" />
                    <x-button icon="o-trash" label="Delete" class="btn-error  btn-sm" wire:click="deleteprogrammeoutcome({{ $outcome->id }})" wire:confirm="Are you sure you want to delete this outcome?" />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">
                    <x-alert class="alert-error" icon="o-exclamation-triangle" title="No outcomes found" />
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
</x-modal>

<x-modal wire:model="indicatormodal" title="{{ $outcome?->title }} indicators" box-class="max-w-5xl">
    <x-hr/>
    <x-form wire:submit="saveindicator">
        <div class="grid grid-cols-4 gap-2">
        <x-input wire:model="indicator"  placeholder="Enter indicator" />
        <x-input wire:model="target"  placeholder="Enter target">
            <x-slot:append>
                <x-select wire:model="uom" placeholder="Select unit of measure" :options="[['id'=>'EACH','name'=>'EACH'],['id'=>'%','name'=>'%']]" option-label="name" option-value="id" />
            </x-slot:append>
        </x-input>
        <x-input wire:model="variance"  placeholder="Enter variance">
            <x-slot:prepend>
                <x-select wire:model="varianceuom" placeholder="Select variance" :options="[['id'=>'+/-','name'=>'+/-'],['id'=>'+','name'=>'+'],['id'=>'-','name'=>'-']]" option-label="name" option-value="id" />
            </x-slot:prepend>
        </x-input>
        <x-button type="submit" label="{{ $indicator_id ? 'Edit Indicator' : 'Add Indicator' }}" class="btn-primary" spinner="saveindicator" />
    </div>
    </x-form>
    <table class="table mt-2 table-zebra">
        <thead>
            <tr>
                <th>Indicator</th>
                <th>Target</th>
                <th>Variance</th>
                <th>UOM</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($outcome?->indicators??[] as $indicator)
            <tr>
                <td>{{ $indicator->indicator }}</td>
                <td>{{ $indicator->target }}</td>
                <td>{{ $indicator->variance }} {{ $indicator->varianceuom }}</td>
                <td>{{ $indicator->uom }}</td>
                <td class="flex justify-end gap-2">
                    <x-button icon="o-pencil" label="Edit" class="btn-info btn-sm" wire:click="editindicator({{ $indicator->id }})" />
                    <x-button icon="o-trash" label="Delete" class="btn-error btn-sm" wire:click="deleteindicator({{ $indicator->id }})" wire:confirm="Are you sure you want to delete this indicator?" />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">
                    <x-alert class="alert-error" icon="o-exclamation-triangle" title="No indicators found" />
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
   
</x-modal>

<x-modal wire:model="outcomemodal" title="{{ $outcome_id ? 'Edit Outcome' : 'Add Outcome' }}">
    <x-form wire:submit="saveoutcome">
        <div class="grid gap-2">
            <x-input label="Title" wire:model="title" />
        </div>
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.closeModal()" />
            <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-form>
</x-modal>
</div>
    
