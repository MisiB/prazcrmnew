<div>
   <x-breadcrumbs :items="$breadcrumbs" 
    class="bg-base-300 p-3 mt-2 rounded-box"/>

<x-card title="Departmental Outputs" separator class="mt-5">
<x-slot:menu>    
    <x-button label="Get Subprogramme Outputs" wire:click="modal=true" class="btn-primary" />
</x-slot:menu>

    @forelse (collect($subprogrammeoutputs) as $subprogrammeoutput)
    <x-card title="{{ $subprogrammeoutput['programmeoutcomeindicator'] }}" subtitle="Target: {{ $subprogrammeoutput['programmeoutcomeindicatortarget'] }} {{ $subprogrammeoutput['programmeoutcomeindicatoruom'] }} Acceptable Variance: {{ $subprogrammeoutput['programmeoutcomeindicatorvarianceuom'] }}{{ $subprogrammeoutput['programmeoutcomeindicatorvariance'] }}{{ $subprogrammeoutput['programmeoutcomeindicatoruom'] }} " separator class="mt-3 rounded-lg border-2 border-green-300">
        <x-slot:menu>
            <x-button label="Add Output" wire:click="addoutput({{ $subprogrammeoutput['subprogramme_id'] }})" class="btn-primary btn-outline" />
        </x-slot:menu>
        <x-card class="border-2 border-gray-200">
    <table class="table table-zebra table-sm">
        <thead>
            <tr>
                <th>Code</th>
                <th>Programme</th>
                <th>Outcome</th>
                <th>Department</th>
                <th>Weightage</th>
            </tr>
        </thead>
        <tbody>
    <tr>
        <td>{{ $subprogrammeoutput['programmecode'] }}</td>
        <td>{{ $subprogrammeoutput['programme'] }}</td>
        <td>{{ $subprogrammeoutput['programmeoutcome'] }}</td>
        <td>{{ $subprogrammeoutput['subprogramme'] }}</td>
        <td>{{ $subprogrammeoutput['weightage'] }}%</td>
        <td>
           
        </td>
        
    </tr>
    </tbody>
    </table>
        </x-card>
    <x-card class="mt-2 border-2 border-gray-200">
        <div class="font-bold">Outputs</div>
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Output</th>
                <th>Indicator</th>
                <th>Quantity</th>
                <th>Target</th>
                <th>Allowable Variance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subprogrammeoutput['data'] as $data)
            <tr>
                <td>{!! $data['subprogrammeoutput'] !!}</td>
                <td>{{ $data['subprogrammeoutputindicator'] }}</td>
                <td>{{ $data['subprogrammeoutputquantity'] }}</td>
                <td>{{ $data['subprogrammeouttarget'] }}</td>
                <td>{{ $data['subprogrammeoutallowablevariance'] }}</td>
                <td class="flex gap-2">
                    <x-button icon="o-pencil"  class="btn-info btn-outline btn-sm" wire:click="getoutput({{ $data['id'] }})" />
                    <x-button icon="o-trash"  class="btn-error btn-outline btn-sm" wire:click="deleteoutput({{ $data['id'] }})" wire:confirm="Are you sure you want to delete this output?" />
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="p-4 bg-red-300 text-center">
                        No Outputs found
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </x-card>
    </x-card>
    @empty
      <div class="p-4 bg-red-300 text-center">
                        No data retrieved please click on get subprogramme outputs 
                    </div>
    @endforelse
   
</x-card>
<x-modal title="Search parameters" wire:model="modal">
    <x-form wire:submit.prevent="getsubprogrammeoutputs">
        <div class="grid gap-2">
            <x-select label="Strategy" wire:model="strategy_id" placeholder="Select Strategy" :options="$strategies" option-label="name" option-value="id" />
            <x-input label="Year" wire:model="year" type="number" />
        </div>
        <x-slot:actions>
            <x-button label="Close" wire:click="$wire.closeModal()" class="btn-outline" />
            <x-button label="Search" type="submit" class="btn-primary" spinner="getsubprogrammeoutputs" />
        </x-slot:actions>
    </x-form>
</x-modal>

<x-modal title="{{ $output_id ? 'Edit Output' : 'Add Output' }}" wire:model="createModal" box-class="max-w-3xl">
    <x-form wire:submit.prevent="save">
        <div class="grid grid-cols-2 gap-2">
        
                  <x-input label="Indicator" wire:model="indicator" type="text" />
            <x-input label="Quantity" wire:model="quantity" type="number" />
            <x-input label="Target" wire:model="target" type="number" />
            <x-input label="Allowable Variance" wire:model="variance" type="number" />
            
        </div>
        <div class="grid gap-2">
            <x-textarea wire:model="output" label="Outputs" hint="Capture departmental outputs" />
        </div>
        <x-slot:actions>
            <x-button label="Close" wire:click="$wire.closeModal()" class="btn-outline" />
            <x-button label="{{ $output_id ? 'Update' : 'Save' }}" type="submit" class="btn-primary" spinner="create" />
        </x-slot:actions>
    </x-form>
</x-modal>
</div>
    
