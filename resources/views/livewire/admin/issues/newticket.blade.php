<div>
    <x-button label="New Ticket" @click="$wire.newdrawer = true" responsive icon="o-plus" class="btn-ghost"/>
    <x-modal wire:model="newdrawer" title="New Issue" right separator with-close-button class="lg:w-full">
        <x-form wire:submit="SaveRecord">
            <div class="grid grid-cols-2 gap-2">
                <x-select label="Issue group" :options="$groups" option-value="id" option-label="name" placeholder="select issue group" wire:model="issuegroupid"/>
                <x-select label="Issue type" :options="$types" option-value="id" option-label="name" placeholder="select issue type" wire:model="issuetypeid"/>
            </div>
            <div class="grid grid-cols-2 gap-2">
                @if(strtolower(Auth::user()->level)==="bidder"||strtolower(Auth::user()->level)==="entity")
                    <x-input Label="Organization name" wire:model="name" icon="o-sun" readonly/>
                    <x-input Label="Organization PRAZ Number" wire:model="regnumber" icon="o-sun" readonly/>
                @else
                    <x-input Label="Organization name" wire:model="name" icon="o-sun"/>
                    <x-input Label="Organization PRAZ Number" wire:model="regnumber" icon="o-sun"/>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-2">
                <x-input Label="Submitter email" wire:model="email" icon="o-sun" readonly/>
                <x-input Label="Submitter contact" wire:model="phone" icon="o-sun"/>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <x-input Label="Issue title" wire:model="title" icon="o-sun"/>
                <x-select label="Select attachment type" :options="$attachmenttypelist" placeholder="select option" wire:model.lazy="attachmenttype"/>

            </div>
            <div class="grid gap-5">
                <x-textarea
                    label="Issue description"
                    wire:model="description"
                    rows="5"
                    />
            </div>

            <div class="grid gap-5">
                @if($attachmenttype=="1")
                <x-image-library
                    wire:model="files" {{-- Temprary files --}}
                wire:library="library" {{-- Library metadata property --}}
                    :preview="$library" {{-- Preview control --}}
                    label="Supporting images"
                    hint="Max 100Kb"/>
                    @elseif($attachmenttype=="2")
                    <x-file wire:model="attachments" label="Documents" multiple />
                @endif
            </div>
            <x-slot:actions>
                <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
                <x-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="SaveRecord"/>
            </x-slot:actions>
        </x-form>
    </x-drawer>

</div>
