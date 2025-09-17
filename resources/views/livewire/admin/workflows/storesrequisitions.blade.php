<div>


  <x-card class="mt-2">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <h1 class="text-2xl font-bold text-gray-800">
          @php
              $hour = date('H');
              $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
          @endphp
          {{ $greeting }}, {{ auth()->user()->name ?? 'Admin' }}!
        </h1>
        <p class="mt-2 text-gray-800 text-sm opacity-90">
          Welcome to your leave applications dashboard. Here's an overview of your requests.
        </p>
        <x-breadcrumbs :items="$breadcrumbs" class="bg-base-300 p-3 mt-2 rounded-box" link-item-class="text-sm font-bold" />
      </div>

      <div class="text-gray-800 text-sm opacity-90">
        {{ now()->format('l, F j, Y') }}
      </div>
    </div>
  </x-card>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 mt-4">
        <x-card>
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 p-3 rounded-full">
                    <x-icon name="o-clock" class="w-8 h-8 text-blue-600"/>
                </div>
                <div class="text-xl font-bold text-gray-700">{{ $totalpending }}</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center space-x-3">
                <div class="bg-yellow-100 p-3 rounded-full">
                    <x-icon name="o-play" class="w-8 h-8 text-yellow-600"/>
                </div>
                <div class="text-xl font-bold text-gray-700">{{ $totalapproved }}</div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
        </x-card>

        <x-card>
        <div class="flex items-center space-x-3">
            <div class="bg-green-100 p-3 rounded-full">
                <x-icon name="o-check" class="w-8 h-8 text-green-600"/>
            </div>
            <div class="text-xl font-bold text-gray-700">{{ $totalrejected }}</div>
            <div class="text-sm text-gray-600">Rejected</div>
        </div>
        </x-card>
  </div>

  <div>
        <x-card title="Departmental Requisitions" separator class="mt-5 border-2 border-gray-200">
            <x-slot:menu>
                <x-input wire:model.live="year" min="2020" max="{{ now()->year }}" placeholder="Select Year" type="number" />
                <x-select wire:model.live="statusfilter" placeholder="Filter by status" :options="$statuslist" option-label="name" option-value="id" />
                <x-button icon="o-plus" label="Add Stores Requisition" class="btn-primary" wire:click="addrequisitionmodal=true"/>
            </x-slot:menu>

            <x-table :headers="$headers" :rows="$storesrequisitions">
                @scope('cell_status', $storesrequisition)
                    @if($storesrequisition->status=='A')
                        <span class="badge badge-success">Approved</span>
                    @elseif($storesrequisition->status=='P')
                        <span class="badge badge-warning">Pending</span>
                    @else
                        <span class="badge badge-error">Rejected</span>
                    @endif
                @endscope 
                @scope('cell_initiator',$storesrequisition)   
                    <span>{{$storesrequisition->initiator->name." ".$storesrequisition->initiator->surname}}</span>
                @endscope               
                <x-slot:empty>
                    <x-alert class="alert-error" title="No leave requests found." />
                </x-slot:empty>
            </x-table>
        </x-card>    
  </div>

    <x-modal wire:model="addrequisitionmodal"  title="STORES REQUISITION FORM">
        <x-form wire:submit="sendrequisition">
            <div class="grid  gap-4" separator>
                <x-button label="Send" type="submit" class="btn-primary" spinner="sendrequisition" /> 
                <x-input  wire:model.live="requiredquantity" hint="Required quantity"></x-input>
                <x-input  wire:model.live="itemdetail" hint="Item detail"></x-input>
                <x-input  wire:model.live="purposeofrequisition" hint="Purpose of requisition"></x-input>
                <div class="h-32">
                    <x-signature wire:model.live="employeesignature" hint="EMPLOYEE. Please, sign here" class="h-full"/>   
                </div>
                
                @can('approvalflow.requisition.hod')
                    <div></div>
                    <div>
                        <x-select :options="$hodassigneesmap" wire:model.live.debounce="assignedHodId"/>
                    </div>
                @endcan 
                <div></div>
                
            </div>
        </x-form>
        
    </x-modal>  

</div>