<div>
    <div class="text-sm breadcrumbs">
        <ul class="flex">
            <li>
                <x-button label="Home" link="{{ route('admin.home') }}" class="btn-ghost" icon="o-home"/>
            </li>
            <li>   <x-button label="Assigned Issues" class="btn-ghost"/></li>
        </ul>
    </div>
 
    <x-modulewelcomebanner :breadcrumbs="$breadcrumbs"/>
    <x-card shadow separator title="My Tasks" class="mt-4">
        @if ($error !="")
        <x-alert title="Error" description="{{ $error }}" icon="o-exclamation-triangle" class="alert-error" />   
        @endif

        @if($success !="")
        <x-alert title="Success" description="{{ $success }}" icon="o-check-circle" class="alert-success" /> 
        @endif
        <x-slot:menu>
            <x-button label="export" x-on:click="$wire.export()" responsive icon="o-plus" class="btn-primary"/>
            <x-button label="import" @click="$wire.importdrawer = true" responsive icon="o-plus" class="btn-success"/>

        </x-slot:menu>
        <x-table :headers="$headers" :rows="$tasks" separator progress-indicator show-empty-text empty-text="No Assigned Issues Here!">
            @scope('cell_issuetype',$task)
            {{$this->issuetype->find($task->issuelog?->issuetype_id)->name ?? '-'}}
            @endscope
            @scope('cell_Ticket',$task)
            {{$task->issuelog?->ticket ?? '-'}}
            @endscope
            @scope('cell_Name',$task)
                {{$task->issuelog?->name ?? '-'}}
            @endscope
            @scope('cell_Title',$task)
                {{$task->issuelog?->title ?? '-'}}
            @endscope
            @scope('cell_Status',$task)
                @if($task->status=="PENDING")
                    <x-badge :value="$task->status" class="badge-primary"/>
                @else
                    <x-badge :value="$task->status" class="badge-success"/>
                @endif
            @endscope
            @scope('cell_Issuestatus',$task)
                @if($task->issuelog?->status=="PENDING")
                    <x-badge :value="$task->issuelog->status" class="badge-primary"/>
                @else
                    <x-badge :value="$task->issuelog->status" class="badge-success"/>
                @endif
            @endscope
            @scope('cell_created_at',$task)
            {{$task->created_at?->diffForHumans() ?? '-'}}
            @endscope
            @scope('actions', $task)
            <div class="flex items-center justify-center">

                <x-button icon="s-magnifying-glass-circle" link="{{route('admin.issues.viewassignedissue',$task->issuelog?->ticket)}}" spinner
                          label="View" class="text-blue-500 btn-ghost btn-sm"/>

            </div>
            @endscope
        </x-table>
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
                    <img src="../{{ $image }}"/>
                @empty
                    
                @endforelse
                </x-card>

            </div>
        @endif

        @if(count($uploadfiles)>0)
            <x-card title="Supporting documentation">
            <div class="grid grid-cols-4 gap-4 mb-5">

                    @foreach($uploadfiles as $upload)
                        <x-button class="btn-outline" link="../{{$upload['url']}}">
                            Download document
                        </x-button>
                    @endforeach

            </div>
            </x-card>
        @endif
        @if ($task!=null)
            <x-card title="Comments">
                @if($task->issuelog != null)
                @forelse ($task?->issuelog?->comments as $comment )
                <div class="p-3 bg-gray-300 rounded-md shadow-md">
                    <small>{{ $comment->user->name }} {{ $comment->user->surname }}</small> 
                    <div>{{ $comment->comment }}</div>
                    <i>{{ $comment->created_at }}</i>
                  </div>  
                @empty
                   <div class="p-3 bg-gray-300 rounded-md shadow-md">
                     <div>No comments found</div>
                   </div> 
                @endforelse
                <x-form wire:submit="update">
                    <div class="grid gap-5">
                        <x-textarea
                            label="Comment"
                            wire:model="comment"
                            rows="5"
                            inline/>
                    </div>
                    <x-slot:actions>
                        <x-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="SaveRecord"/>
                    </x-slot:actions>
                </x-form>
                @endif
            </x-card>
        @endif
        <x-card title="Make Decision">
            <x-form wire:submit="update">
                <div class="grid gap-5">
                    <x-select label="Status" :options="$statuslist" wire:model="status"/>
                </div>
                <div class="grid gap-5">
                    <x-textarea
                        label="Comment"
                        wire:model="comment"
                        rows="5"
                        inline/>
                </div>
                <x-slot:actions>
                    <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
                    <x-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="SaveRecord"/>
                </x-slot:actions>
            </x-form>
        </x-card>
    </x-drawer>
    <x-drawer wire:model="importdrawer" title="Import Issues" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="import">
            <x-file wire:model.live="file" label="Attach File"/>
            <x-slot:actions>
                <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
                <x-button label="Upload" icon="o-check" class="btn-primary" type="submit" spinner="import"/>
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
