<div>
    <div class="text-sm breadcrumbs">
        <ul class="flex">
            <li>
                <x-button label="Home" link="{{ route('admin.home') }}" class="btn-ghost" icon="o-home"/>
            </li>
            <li><x-button label="Issue logs"  class="btn-ghost" /></li>
        </ul>
    </div>
  
    <x-modulewelcomebanner :breadcrumbs="$breadcrumbs"/>
    @can("issuelog.access")
    <x-input label="Ticket Search Filter"  class="mt-4 mb-4"  wire:model.live.debounce="search" icon-right="o-magnifying-glass" placeholder="Search issues by [Ticket id/ Title/ Description/ Registration number/ Issue type]"/>
    <x-card>
    <x-tabs wire:model="selectedTab">
        <x-tab name="pending-tab">
            <x-slot:label>  
                Pending issues
                <x-badge value="{{ count($logs) }}" class="badge-primary" />
            </x-slot:label>
            @forelse ( $logs as $log )
            <div class="p-3 mt-2 bg-gray-100 rounded-md shadow-sm ">
                <div class="grid grid-cols-2">
                   <b> {{ $log->name }}</b>
                    
                </div>
                <div class="flex justify-between">
                  <div>
                    <div class="mt-4">{{ $log->title }}</div>
                    <div class="pl-4 my-2">{{ $log->description }}</div>
                    <div class="pl-4 grid grid-flow-col mt-1">
                        <x-badge value="Type: {{ $this->issuetype->find($log->issuetype_id)->name ?? '-' }}" />
                        <x-badge value="Reg: {{ $log->regnumber }}" />
                        <x-badge value="Created: {{ $log->created_at->diffForHumans() }}" />
                    </div>
         
                  </div>
                  <div>
                    <x-button icon="s-magnifying-glass-circle" link="{{ route('admin.issues.log',$log->ticket) }}" spinner="showissue"
                        label="Open" class="text-white btn-primary btn-sm"/>
                  </div>
                </div>
            </div>
        
        @empty
            <x-alert title="No Tickets Logged Yet!" icon="o-exclamation-triangle" class="alert-info alert-soft" />
        @endforelse
        
        </x-tab>
        <x-tab name="tricks-tab">
            <x-slot:label>  
                Assigned issues
                <x-badge value="{{ count($assignedlogs) }}" class="bg-blue-100" />
            </x-slot:label>
            @forelse ( $assignedlogs as $log )
            <div class="p-3 mt-2 bg-blue-100 rounded-md ">
                <div>
                   <b> {{ $log->name }}</b>
                </div>
                <div class="flex justify-between">
                  <div>
                    <div class="mt-4">{{ $log->title }}</div>
                    <div class="pl-4 my-2">{{ $log->description }}</div>
                    <div class="pl-4 grid grid-flow-col mt-1">
                        <x-badge value="Type: {{ $this->issuetype->find($log->issuetype_id)->name ?? '-' }}" /><x-badge value="Reg: {{ $log->regnumber }}" /><x-badge value="Created: {{ $log->created_at->diffForHumans() }}" /><x-badge value="Assinged to:  {{$log->task?->user?->name}} {{$log->task?->user?->surname}}"/><x-badge value="Assinged :  {{$log->task?->created_at->diffForHumans()}}"/></div>
         
                  </div>
                  <div>
                    <x-button icon="s-magnifying-glass-circle" link="{{ route('admin.issues.log',$log->ticket) }}" spinner="showissue"
                        label="Open" class="text-white btn-primary btn-sm"/>
                  </div>
                </div>
            </div>
        
        @empty
            <x-alert title="No Tickets Logged Yet!" icon="o-exclamation-triangle" class="alert-info alert-soft" />
        @endforelse
        </x-tab>
        <x-tab name="musics-tab" >
            <x-slot:label>  
                Resolved issues
                <x-badge value="{{ count($resolvedlogs) }}" class="bg-orange-300" />
            </x-slot:label>
            @forelse ( $resolvedlogs as $log )
            <div class="p-3 mt-2 bg-blue-100 rounded-md ">
                <div>
                   <b> {{ $log->name }}</b>
                </div>
                <div class="flex justify-between">
                  <div>
                    <div class="mt-4">{{ $log->title }}</div>
                    <div class="pl-4 my-2">{{ $log->description }}</div>
                    <div class="pl-4 grid grid-flow-col mt-1">
                        <x-badge value="Type: {{ $this->issuetype->find($log->issuetype_id)->name ?? '-' }}" /><x-badge value="Reg: {{ $log->regnumber }}" /><x-badge value="Created: {{ $log->created_at->diffForHumans() }}" /><x-badge value="Assinged to:  {{$log->task?->user?->name}} {{$log->task?->user?->surname}}"/>  <x-badge value="Assinged :  {{$log->task?->created_at->diffForHumans()}}"/></div>

                  </div>
                  <div class="flex justify-between">
                    <x-button icon="s-magnifying-glass-circle" link="{{ route('admin.issues.log',$log->ticket) }}" spinner="showissue"
                        label="Open" class="text-white btn-primary btn-sm"/>
                        <x-button icon="o-check-circle" x-on:click="$wire.closeissue('{{$log->ticket}}')" spinner="closeissue"
                            label="Close" class="text-white bg-green-500 btn-sm"/>
                  </div>
                </div>
            </div>
        
        @empty
            <x-alert title="No Tickets Logged Yet!" icon="o-exclamation-triangle" class="alert-info alert-soft" />
        @endforelse
        </x-tab>
        <x-tab name="closed-tab" >
            <x-slot:label>  
                Closed issues
                <x-badge value="{{ count($closedlogs) }}" class="bg-green-400" />
            </x-slot:label>
            @forelse ( $closedlogs as $log )
            <div class="p-3 mt-2 bg-blue-100 rounded-md ">
                <div>
                   <b> {{ $log->name }}</b>
                </div>
                <div class="flex justify-between">
                  <div>
                    <div class="mt-4">{{ $log->title }}</div>
                    <div class="pl-4 my-2">{{ $log->description }}</div>
                    <div class="pl-4 grid grid-flow-col mt-1">
                        <x-badge value="Type: {{ $this->issuetype->find($log->issuetype_id)->name ?? '-' }}" /><x-badge value="Reg: {{ $log->regnumber }}" /><x-badge value="Created: {{ $log->created_at->diffForHumans() }}" /><x-badge value="Assinged to:  {{$log->task?->user?->name}} {{$log->task?->user?->surname}}"/>  <x-badge value="Assinged :  {{$log->task?->created_at->diffForHumans()}}"/></div>
         
                  </div>
                  <div class="flex justify-between">
                    <x-button icon="s-magnifying-glass-circle" 
                    link="{{ route('admin.issues.log',$log->ticket) }}" spinner="showissue" label="Open" class="text-white btn-primary btn-sm"/>
                  </div>
                </div>
            </div>
            
            @empty
                <x-alert title="No Tickets Logged Yet!" icon="o-exclamation-triangle" class="alert-info alert-soft" />
            @endforelse
        </x-tab>
    </x-tabs>
        
    @else
        <x-alert title="Restricted Resource" description="Unauthorized to access resource" icon="o-home"
                 class="alert-error"/>
    @endcan
    </x-card>
    <x-drawer wire:model="drawer" title="Issue" right separator with-close-button class="lg:w-2/3">
        <div class="grid grid-cols-2 gap-2 mb-4">
            <x-input Label="Name" wire:model="name" icon="o-sun" readonly/>
            <x-input Label="Regnumber" wire:model="regnumber" icon="o-sun" readonly/>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-4">
            <x-input Label="Email" wire:model="email" icon="o-sun" readonly/>
            <x-input Label="Phone number" wire:model="phone" icon="o-sun" readonly/>
        </div>
        <div class="grid gap-5 mb-4">
            <x-input Label="Issue title" wire:model="title" icon="o-sun" readonly/>
        </div>
        <div class="grid gap-5 mb-4">
            <x-textarea
                label="Issue description"
                wire:model="description"
                rows="5"
                inline readonly/>
        </div>
        @if(count($images)>0)
            <div class="grid gap-5 mb-4">
                <x-card title="Screen shots">
                    @forelse ($images as $image )
                        <img src="/{{ $image }}"/>
                    @empty
                        
                    @endforelse
                </x-card>

            </div>
        @endif
        @can("Issuelog.Assign")
            <x-form wire:submit="saverecord">
                <x-select label="Assign User" :options="$users" wire:model="userId"/>
                <x-slot:actions>
                    <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
                    <x-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="saverecord"/>
                </x-slot:actions>
            </x-form>
        @endcan


    </x-drawer>
</div>
 