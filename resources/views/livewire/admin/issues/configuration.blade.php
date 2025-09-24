<div>
    <div class="text-sm breadcrumbs">
        <ul class="flex">
            <li> <x-button label="Home" link="/admin/Home" class="btn-ghost" icon="o-home" /></li>
            <li><x-button label="Configurations"  class="btn-ghost" /></li>
        </ul>
    </div>
     
    <x-modulewelcomebanner :breadcrumbs="$breadcrumbs"/>

    @can("issueconfig.access")
     <x-card class="mt-4">
        <x-tabs wire:model="selectedTab">
            <x-tab name="issuegroup-tab" label="Issue Groups" icon="o-users">
                <livewire:admin.issues.issuegroups/>
            </x-tab>
            <x-tab name="issuetype-tab" label="Issue Types" icon="o-sparkles">
                <livewire:admin.issues.issuetypes/>
            </x-tab>
        </x-tabs>

    @else
        <x-alert title="Restricted Resource" description="Unauthorized to access resource" icon="o-home" class="alert-error" />

    @endcan
     </x-card>
</div>
 