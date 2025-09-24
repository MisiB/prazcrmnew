<div>
    <x-card title="Issue types" shadow separator>
        <x-slot:menu>
            <x-button icon="o-plus" @click="$wire.newdrawer = true" class="btn-circle btn-outline"/>
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$types" separator progress-indicator show-empty-text empty-text="Nothing Here!">

            @scope('actions', $menu)
            <div class="flex items-center justify-center">
                @can("modify.issuetype")
                    <x-button icon="o-trash" wire:click="delete({{$menu->id}})" wire:confirm="Are you sure?" spinner
                              class="text-red-500 btn-ghost btn-sm"/>
                    <x-button icon="o-pencil" x-on:click="$wire.edit({{$menu->id}})" spinner
                              class="text-blue-500 btn-ghost btn-sm"/>
                @endcan
            </div>
            @endscope
        </x-table>
    </x-card>
    <x-drawer wire:model="newdrawer" title="New Issue type" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="Save">
            <div class="grid gap-5">
                <x-input Label="Name" wire:model="name" icon="o-sun"/>
            </div>
            <x-slot:actions>
                <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
                <x-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="SaveRecord"/>
            </x-slot:actions>
        </x-form>
    </x-drawer>
    <x-drawer wire:model="editdrawer" title="Edit Issue type" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="Update">
            <div class="grid gap-5">
                <x-input Label="Name" wire:model="name" icon="o-sun"/>
            </div>
            <x-slot:actions>
                <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
                <x-button label="Update" icon="o-check" class="btn-primary" type="submit" spinner="SaveRecord"/>
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
