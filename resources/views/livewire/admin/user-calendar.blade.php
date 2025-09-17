<div class="min-h-screen">
    <!-- Modern Header -->
    <div class="bg-white  mb-3 mt-6 p-2 border-2 border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Weekly Tasks</h1>
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                        Week {{ $currentweek->week }}
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ $currentweek->start_date }} - {{ $currentweek->end_date }}
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative">
                
                </div>
                <x-select wire:model.live="week_id" placeholder="Filter by Week" :options="$weeks" option-label="week" option-value="id" class="w-48" />
            </div>
        </div>
    </div>
    @if($currentweek->calenderworkusertasks->count() ==0)
    <x-alert title="Awaiting approval" description="Your supervisor has not approved your tasks for this week" icon="o-envelope" class="alert-error">
      <x-slot:actions>
          <x-button label="Send for Approval" wire:click="sendforapproval" wire:confirm="Are you sure you want to send for approval?" />
      </x-slot:actions>
  </x-alert>
  @elseif($currentweek->calenderworkusertasks->first()->status == 'pending')
  <x-alert title="Pending approval" description="Your supervisor is reviewing your tasks for this week" icon="o-envelope" class="alert-warning">
    <x-slot:actions>
      @if($currentweek->calenderworkusertasks->first()->comment)
        <x-button label="View comment" wire:click="viewcommentmodal=true" />
      @endif
    </x-slot:actions>
</x-alert>
    @endif
              <div class="grid lg:grid-cols-5 gap-1 mt-2">
            @foreach ($currentweek->calendardays as $day)
          <x-card title="{{ Carbon\Carbon::parse($day->maindate)->format('l') }}" subtitle="{{ $day->maindate }}" class="border-2 rounded-none h-screen border-gray-200" separator>
            <x-slot:menu>
             
                <x-button icon="o-plus" class="btn-ghost btn-sm" wire:click="openModal({{ $day->id }})" />
              
            </x-slot:menu>
               @forelse ($day->tasks??[] as $task)
               <div class="p-2 border border-black rounded-2xl text-sm mt-2">
               <div> {{ $task->title }}</div>
               <x-hr/>

               <div class="mt-1" >Status: <x-badge value="{{ $task->status }}" class=" {{ $task->status == 'pending' ? 'badge-warning' : 'badge-success' }} badge-sm " /></div>
               <div class="mt-1" >Priority: <x-badge value="{{ $task->priority }}" class="{{ $task->priority == 'High' ? 'badge-error' : 'badge-warning' }} badge-sm" /></div>
               
                @if($task->status != 'completed')
                <div class="mt-1" >
                  <x-button icon="o-check" class="btn-ghost btn-sm" wire:click="openmarkmodal({{ $task->id }})" wire:confirm="Are you sure you want to change status of task" />
                
                    <x-button icon="o-pencil" class="btn-ghost btn-sm" wire:click="edit({{ $task->id }})" />
                      @if($currentweek->calenderworkusertasks->count() ==0)
                  <x-button icon="o-trash" class="btn-ghost btn-sm" wire:click="delete({{ $task->id }})" wire:confirm="Are you sure you want to delete this task?" />
                    @endif
                           </div>
                @endif
              </div>
               
               @empty
               <x-alert title="No tasks found." class="alert-error" />
               @endforelse
          </x-card>
        @endforeach 
        </div>

        
        <x-modal wire:model="modal" title="{{ $id ? 'Edit Task' : 'Add Task' }}" box-class="max-w-4xl">
          <x-form wire:submit="save">
            <div class="grid gap-2">
              <x-input wire:model="title" label="Title" />
              <x-select wire:model="priority" label="Priority" placeholder="Select Priority" :options="[['id' => 'High', 'name' => 'High'], ['id' => 'Medium', 'name' => 'Medium'], ['id' => 'Low', 'name' => 'Low']]" option-label="name" option-value="id" />
            </div>
            <div class="grid  gap-2">
              <x-textarea wire:model="description" label="Description" />
            </div>
            <div class="grid  gap-2">
              <x-checkbox label="Tick if this task is linked to an activity in your workplan ?" wire:model.live="link" />   
            </div>
            @if($link)
            <div class="grid gap-2">
              <x-select wire:model="individualoutputbreakdown_id" label="Strategic individual output" placeholder="Select Strategic individual output" :options="$breakdownlist" option-label="description" option-value="id" />
     
            </div>
            @endif
            <x-slot name="actions">
              <x-button label="Close" wire:click="$wire.closeModal()" class="btn-outline" />
              <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot>
          </x-form>
        
        </x-modal>

        <x-modal title="Change Status Task" wire:model="markmodal">
          <div class="grid gap-2">
         
          <x-button icon="o-check" label="Completed" class="btn-success w-full " wire:click="marktaskascompleted({{ $taskid }})" wire:confirm="Are you sure you want to mark this task as completed?" />
          <x-button icon="o-clock" label="Ongoing" class="btn-warning w-full " wire:click="marktaskasongoing({{ $taskid }})" wire:confirm="Are you sure you want to mark this task as ongoing?" />
          <x-button icon="o-clock" label="Pending" class="btn-error w-full " wire:click="marktaskaspending({{ $taskid }})" wire:confirm="Are you sure you want to mark this task as pending?" />
  
          </div>
        </x-modal>

        <x-modal title="View Comment" wire:model="viewcommentmodal">
          <div class="grid gap-2">
            @if($currentweek->calenderworkusertasks->count() > 0)
             {{ $currentweek->calenderworkusertasks->first()->comment }}
            @endif
          </div>
        </x-modal>
 
    
</div>