<div>
    <div class="text-sm breadcrumbs">
        <ul class="flex">
            <li>
                <x-button label="Home" link="{{ route('admin.home') }}" class="btn-ghost" icon="o-home"/>
            </li>
            <li>
                <x-button label="Issue logs" link="{{ route('admin.issues.logs') }}" class="btn-ghost" icon="o-home"/>
            </li>
            <li>   <x-button label="Issue log" class="btn-ghost"/></li>
        </ul>
    </div>
    <x-card title="Issue">
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
                    rows="8"
                    readonly/>
            </div>
        </x-card>

        <x-card class="mt-4">
            <x-tabs wire:model="selectedTab">
                @if(strtolower($user->level)==='entity' || strtolower($user->level)==='bidder')
                @else
                <x-tab name="comments-tab">
                    <x-slot:label>  
                    Comments
                    @if($issuelog != null)
                        <x-badge value="{{count($issuelog?->comments)}}" class="badge-primary" />
                        @endif
                    </x-slot:label>
                        <x-card>
                            @if($issuelog != null)
                            @forelse ($issuelog?->comments as $comment )
                            <div class="p-3 mt-2 bg-gray-100 rounded-md shadow-sm">
                                <b><i>User:</i> {{ $comment->user->name }} {{ $comment->user->surname }}</b> 
                                <div><i>Comment:</i> {{ $comment->comment }}</div>
                                <b><i>Created on: {{ $comment->created_at }}</i></b>
                            </div>  
                            @empty
                            <div class="p-3 bg-gray-300 rounded-md shadow-md">
                                <div>No comments found</div>
                            </div> 
                            @endforelse
                            <x-form wire:submit="Savecomment">
                                <div class="grid gap-5 mt-3">
                                    <x-textarea
                                        label="Comment"
                                        wire:model="issuecomment"
                                        rows="5"
                                        inline/>
                                </div>
                                <x-slot:actions>
                                    <x-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="Savecomment"/>
                                </x-slot:actions>
                            </x-form>
                            @endif
                        </x-card>
                
                </x-tab>
                @endif
                <x-tab name="tricks-tab">
                    <x-slot:label> 
                    Attachments
                    @if(count($images)>0)
                            <x-badge value="{{count($images)}}" class="badge-primary" />
                            @endif
                            @if(count($uploadfiles)>0)
                            <x-badge value="{{count($uploadfiles)}}" class="badge-primary" />
                            @endif
                    </x-slot:label>
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
                    
                        @if(count($uploadfiles)>0)
                            <x-card>
                        
                
                                    @foreach($uploadfiles as $upload)
                                    <div class="flex justify-between p-2 rounded-sm bg-gray-50">
                                        <div>Supporting documentation</div>
                                        <x-button class="btn-primary" link="/{{$upload['url']}}">
                                            Download 
                                        </x-button>
                                    </div>
                                    @endforeach
                
                            
                            </x-card>
                        @endif
                </x-tab>
                @if(strtolower($user->level)==='entity' || strtolower($user->level)==='bidder')
                @else
                <x-tab name="musics-tab" label="Assign user">
                    <x-card title="Assign user">
                    @if($issuelog->task==null)
                        <x-form wire:submit="SaveRecord">
                            <div class="grid gap-5">
                                <x-select label="Status" :options="$users" option-value="id" option-label="name" placeholder="select status" wire:model="userId"/>
                            </div>
                    
                            <x-slot:actions>
                                <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
                                <x-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="SaveRecord"/>
                            </x-slot:actions>
                        </x-form>
                        @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Assigned On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $issuelog->task->user->name }} {{ $issuelog->task->user->surname }}</td>
                                    <td>{{ $issuelog->task->created_at }}</td>
                                    <td>
                                        <x-button class="text-white bg-red-500" label="Remove" @click="$wire.removeTask({{ $issuelog->task->id }})"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @endif
                    </x-card>
                </x-tab>
                @endif
            </x-tabs>
        </x-card>
</div>
