<div>
    <x-breadcrumbs :items="$breadcrumbs" 
    class="bg-base-300 p-3 mt-2 rounded-box"/>
    <x-card title="Workplans" separator class="mt-5 border-2 border-gray-200">
        <x-slot:menu>
            <x-button label="Get Workplans" wire:click="modal=true" class="btn-primary" />
        </x-slot:menu>

        @forelse (collect($workplans) as $workplan)
        <x-card title="{{ $workplan['subprogrammeoutputindicator'] }}" class="mt-3 rounded-lg border-2 border-green-300" separator>
        <table class="table table-zebra mt-3">
            <thead>
                <tr>
                    <th>Output</th>
                    <th>Target</th>
                    <th>Variance</th>
                </tr>
            </thead>
            <tbody>
        <tr>
            <td>{!! $workplan['subprogrammeoutput'] !!}</td>
            <td>{{ $workplan['subprogrammeouttarget'] }}</td>
            <td>{{ $workplan['subprogrammeoutallowablevariance'] }}</td>        
            
        </tr>
    </tbody>
</table>
<x-card title="Individual outputs" separator class="bg-gray-200 rounded-none">
    <x-slot:menu>
        <x-button label="Add output" wire:click="addworkplan({{ $workplan['id'] }})" class="btn-primary btn-outline" />
    </x-slot:menu>
    <table class="table table-zebra mt-3">
        <thead>
            <tr>
                <th>Output</th>
                <th>Indicator</th>
                <th>Target</th>
                <th>Variance</th>
                <th>Weightage</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($workplan['userindividualoutputs'] as $output)
            <tr>
                <td>{!! $output['output'] !!}</td>
                <td>{{ $output['indicator'] }}</td>
                <td>{{ $output['target'] }}</td>
                <td>+/-{{ $output['variance'] }}</td>
                <td>{{ $output['weightage'] }}%</td>
                <td class="flex space-x-2">
                    <x-button icon="o-pencil" wire:click="editoutput({{ $output['id'] }})" class="btn-primary btn-sm btn-outline" />
                     <x-button icon="o-trash" wire:click="deleteoutput({{ $output['id'] }})" wire:confirm="Are you sure you want to delete this output?" class="btn-error btn-sm btn-outline" />
                   <x-button icon="o-user-group" wire:click="getsubordinates({{ $output['id'] }})" class="btn-info btn-sm btn-outline" />
                   <x-button icon="o-calendar-date-range" wire:click="getbreakdown({{ $output['id'] }})" class="btn-success btn-sm btn-outline" />
</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center p-3 text-red-500">No outputs found</td>
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
        <x-form wire:submit.prevent="getworkplans">
            <div class="grid gap-2">
                <x-select label="Strategy" wire:model="strategy_id" placeholder="Select Strategy" :options="$strategies" option-label="name" option-value="id" />
                <x-input label="Year" wire:model="year" type="number" />
            </div>
            <x-slot:actions>
                <x-button label="Close" wire:click="$wire.closeModal()" class="btn-outline" />
                <x-button label="Search" type="submit" class="btn-primary" spinner="getworkplans" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal title="{{ $id ? 'Edit Workplan' : 'Add Workplan' }}" wire:model="addmodal" box-class="max-w-3xl">
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-2 gap-2">
               
         
                <x-input label="Indicator" wire:model="indicator" type="text" />
                <x-input label="Target" wire:model="target" type="number" />
                <x-input label="Allowable Variance" wire:model="variance" type="number" />
                <x-input label="Weightage" wire:model="weightage" max="100" min="0" type="number" />
            </div>
            <div class="grid  gap-2">
                <x-editor wire:model="output" label="Outputs" hint="Capture departmental outputs" />
            </div>
            <x-slot:actions>
                <x-button label="Close" wire:click="$wire.closeModal()" class="btn-outline" />
                <x-button label="{{ $id ? 'Update' : 'Submit' }}" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
    <x-modal title="Assignees" wire:model="assignemodal" box-class="max-w-3xl">
     <x-card title="Subordinates" separator class="bg-gray-200 rounded-none">
        <table class="table table-zebra mt-3">
            <thead>
                <tr>
                    <th>Subordinate</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subordinates as $subordinate)
                <tr>
                    <td>{{ $subordinate->user->name }} {{ $subordinate->user->surname }}</td>
                    <td>{{ $subordinate->user->email }}</td>
                    <td class ="flex justify-end">
                     @if($assigneelist->contains('user_id', $subordinate->user_id))
                     <div class="text-red-500">Assigned</div>
                     @else
                        <x-button icon="o-plus" wire:click="selectassign({{ $subordinate->id }})" class="btn-primary btn-outline btn-sm" />
                     @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center p-3 text-red-500">No assignees found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
       </x-card>
       <x-card title="Assignees" separator class="bg-gray-200 rounded-none mt-2">
        <table class="table table-zebra mt-3">
            <thead>
                <tr>
                    <th>Assignee</th>
                    <th>Target</th>
                    <th>Variance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assigneelist as $assignee)
                <tr>
                    <td>{{ $assignee->user->name }} {{ $assignee->user->surname }}</td>
                    <td>{{ $assignee->target }}</td>
                    <td>{{ $assignee->variance }}</td>
                    <td class="flex space-x-2 justify-end">
                        <x-button icon="o-pencil" wire:click="editassign({{ $assignee->id }})" class="btn-primary btn-ghost btn-sm" />
                        <x-button icon="o-trash" wire:click="deleteassignee({{ $assignee->id }})" wire:confirm="Are you sure you want to delete this assignee?" class="btn-error btn-ghost btn-sm" />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center p-3 text-red-500">No assignees found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
    </x-modal>
    <x-modal title="{{ $assignee_id ? 'Edit' : 'Add' }}Assign target" wire:model="newassignemodal">
        <x-form wire:submit.prevent="saveassignee">
        <div class="grid gap-2">
        <x-input type="number" wire:model="target" placeholder="Target" />
        <x-input type="number" wire:model="variance" placeholder="Variance" />
        <x-button label="{{ $assignee_id ? 'Update' : 'Submit' }}" type="submit" class="btn-primary" spinner="saveassignee" />
        </div>
            </x-form>
    </x-modal>


    <x-modal title="Workplan breakdown" wire:model="breakdownmodal" box-class="max-w-4xl">

      <table class="table table-zebra mt-3">
        <thead>
            <tr>
                <th>Month</th>
                <th>Description</th>
                <th>Output</th>
                <th>Cont(%)</th>
                <th>Approval</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
       @forelse ($breakdownlist as $breakdown)
       <tr>
           <td>{{ $breakdown['month'] }}</td>
           <td>{{ $breakdown['description'] }}</td>
           <td>{{ $breakdown['output'] }}</td>
           <td>{{ $breakdown['contribution'] }}%</td>
           <td>{{ $breakdown['approvalstatus'] }}</td>
           <td class="flex gap-2">
            <x-button icon="o-pencil" wire:click="editbreakdown({{ $breakdown['id'] }})" class="btn-info btn-outline btn-sm" />
            <x-button icon="o-trash" wire:click="deletebreakdown({{ $breakdown['id'] }})" wire:confirm="Are you sure you want to delete this breakdown?" class="btn-error btn-outline btn-sm" />
        </td>
       </tr>
       @empty
       <tr>
           <td colspan="5" class="text-center p-3 text-red-500">No breakdown found</td>
       </tr>
       @endforelse
</tbody>
</table>
<x-button icon="o-plus" wire:click="addbreakdownmodal=true" class="btn-primary btn-circle" />
    </x-modal>


    <x-modal title="{{ $breakdown_id ? 'Edit' : 'Add' }} Workplan breakdown" wire:model="addbreakdownmodal" box-class="max-w-3xl">
        <x-form wire:submit.prevent="savebreakdown">
            <div class="grid grid-cols-2 gap-2">
                <x-select label="Month" wire:model="month" placeholder="Select Month" :options="[['id'=>'January', 'name'=>'January'], ['id'=>'February', 'name'=>'February'], ['id'=>'March', 'name'=>'March'], ['id'=>'April', 'name'=>'April'], ['id'=>'May', 'name'=>'May'], ['id'=>'June', 'name'=>'June'], ['id'=>'July', 'name'=>'July'], ['id'=>'August', 'name'=>'August'], ['id'=>'September', 'name'=>'September'], ['id'=>'October', 'name'=>'October'], ['id'=>'November', 'name'=>'November'], ['id'=>'December', 'name'=>'December']]" option-label="name" option-value="id" />
                <x-input label="Contribution" wire:model="contribution" type="number" hint="Percentage contribution torwards annual target" />
            </div>
            <div class="grid gap-2">
                <x-input label="Output" wire:model="output" type="text" hint="Overall monthly output" />
                <x-textarea label="Description" wire:model="description" hint="Description of your monthly target" />
            </div>
            <x-slot:actions>
                <x-button label="Close" wire:click="addbreakdownmodal = false" class="btn-outline" />
                <x-button label="{{ $breakdown_id ? 'Update' : 'Submit' }}" type="submit" class="btn-primary" spinner="savebreakdown" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
