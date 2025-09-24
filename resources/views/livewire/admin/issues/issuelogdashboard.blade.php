<div class="w-[70vw]">

    <section class="mt-[50px] mb-[30px] mx-[40px] grid grid-flow-col justify-between">
        @if($requiredIssuetype!=0)
            <x-header title="Showing Dashboard For {{$dashboardTitle}} Issues On Filtered Dates"  size="text-xl" class="text-black"/>
        @else
            <x-header title="Showing Dashboard For All Issues On Filtered Dates"  size="text-xl" class="text-black"/>
        @endif
        <x-button class="justify-self-end w-[10vw] bg-white hover:bg-white text-emerald-950 border-none shadow-sm" label="Filters" @click="$wire.openFilterDrawer = true" responsive icon="o-funnel" />
    </section>
    <!-- Statistics Tables -->
    <section class="grid grid-flow-col  gap-6">

        <x-stat
        title="Praz"
        icon="s-hand-thumb-up"
        value="{{$prazIssues->count()}}"
        tooltip-bottom="Issue Group"
        class="text-black bg-amber-200 border-none shadow-md"
        color="text-blue-700"/>

        <x-stat
        title="Procuring Entity"
        icon="o-arrow-trending-up"
        value="{{$procuringEntityIssues->count()}}"
        tooltip-bottom="Issue Group"
        class="text-black bg-amber-200 border-none shadow-md"
        color="text-blue-700"/>

        <x-stat
        title="Supplier"
        icon="o-chart-pie"
        value="{{$supplierIssues->count()}}"
        tooltip-bottom="Issue Group"
        class="text-black bg-amber-200 border-none shadow-md"
        color="text-blue-700"/>

        <x-stat
        title="General"
        icon="o-scale"
        value="{{$generalIssues->count()}}"
        tooltip-bottom="Issue Group" 
        class="text-black bg-amber-200 border-none shadow-md"
        color="text-blue-700"/>

    </section>
    <!--Columns Container-->
    <div class="grid gap-3 ">
            <!-- Bar Graphs for issues comparison -->
            <section class="grid grid-flow-col gap-6 justify-center mt-[60px]">

                <section class="grid grid-col shadow-md rounded-md pb-4 bg-white">                   
                    <!-- Chart type filter -->
                    <section class="grid grid-row justify-between">
                        <x-dropdown label="Pending Issues Chart Type" class="bg-opacity-0 hover:bg-opacity-0 text-emerald-950 border-none text-md ">
                           
                            <x-menu-item title="All time" wire:click="$set('isDailyPendingChart', false)"  /> 
                            <x-menu-item title="Daily" wire:click="$set('isDailyPendingChart', true)" />
                        </x-dropdown>
                    </section>
                    <x-chart wire:model="pendingIssuesChart" class="w-[35vw]"/>
                </section>
                
                <section class="grid grid-col shadow-md rounded-md pb-4 bg-white">            
                    <!-- Chart type filter -->
                    <section class="grid grid-row justify-between">
                        <x-dropdown label="Resolved Issues Chart Type" class="bg-opacity-0 hover:bg-opacity-0 text-emerald-950 border-none text-md">
                            
                            <x-menu-item title="All time"  wire:click="$set('isDailySettlementsChart', false)"/>
                            <x-menu-item title="Daily" wire:click="$set('isDailySettlementsChart', true)" />
                        </x-dropdown>
                    </section>
                    <x-chart wire:model="resolvedIssuesChart" class="w-[35vw]"/>
                </section>
            </section>

            <!-- Bar Graph for Issue Types -->
            <section class="mt-[80px] p-8 bg-white">
                <x-header title="Issues Perspective"  size="text-xl" class="text-black"/>
                <x-chart wire:model="issuesTypeChart"/>
            </section>

            <!-- Records Tables -->
            {{--The table will be defined by our selected issue type Variable === $requiredIssuetype--}}
            <section class="mt-[80px] shadow-md rounded-md p-8 bg-white">
                <!-- HEADER -->
                <div class="mt-[10px] mb-[30px]">
                    <x-header title="{{$dashboardTitle}} Issue Log Report"  size="text-xl" class="text-black mb-[15px]" progress-indicator>
                    </x-header>
                </div>
                <!-- DATA TABLES -->
                <div>
                    <!-- TABS  -->
                    <x-tabs wire:model="selectedTab">
                        <x-tab name="assignee-tab">
                            <x-slot:label>
                                Assignee Log Report 
                                <x-badge :value="count($assignees)" class="badge-primary text-white font-semibold" />
                            </x-slot:label>

                            <!-- RESPONDENCE RECORDS TABLE  -->
                            <x-card class="bg-opacity-0" wire:click="refresh">
                                <x-table :headers="$assigneeHeaders" :rows="$assignees"/>
                            </x-card>
                        </x-tab>

                        <x-tab name="respondence-tab">
                            <x-slot:label>
                                Respondence Log Report 
                                <x-badge :value="count($respondents)" class="badge-primary text-white font-semibold" />
                            </x-slot:label>

                            <!-- RESPONDENCE RECORDS TABLE  -->
                            <x-card class="bg-opacity-0">
                                <x-table :headers="$respondentHeaders" :rows="$respondents"/>
                            </x-card>
                        </x-tab>

                    </x-tabs>
                </div>
            </section>


    </div>



    <!-- FILTER DRAWER -->
    <x-drawer wire:model="openFilterDrawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div  class="grid grid-cols-6 gap-4">
            <div class="col-start-1 col-end-7">
                <x-input placeholder="Search by issue description..." wire:model.live.debounce="search" icon="o-magnifying-glass" clearable  @keydown.enter="$wire.openFilterDrawer = false" />     
            </div>
            <div class="col-start-1 col-span-3">
                <x-datepicker label="Date from" wire:model.live.debounce="dateFromSearch" icon="o-calendar" :config="$config1" />
            </div>
            <div class="col-end-7 col-span-3">
                <x-datepicker label="Date to" wire:model.live.debounce="dateToSearch" icon="o-calendar" :config="$config1" />      
            </div>
            <div class="col-start-1 col-span-3">
                <!--Issue Type Select-->
                <x-dropdown label="Select issue type">
                    @foreach($issuetypes as $issuetype)
                    <div class="grid grid-flow-col justify-items-start gap-2">
                        <div class="w-[1px]">
                            <x-icon name="o-presentation-chart-line" class="w-auto h-6 text-emerald-950" />
                        </div>
                        <div>
                            <x-menu-item title="{{$issuetype->name}}"  class="text-black mt-[-5px]" wire:click="$set('requiredIssuetype', {{$issuetype->id}})" />    
                        </div>
                    </div>
                    @endforeach
                </x-dropdown>
            </div>  
            <div class="col-start-1 col-span-3">
                <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
           </div>
            <div class="col-end-7 col-span-3">
                <x-button label="Done" icon="o-check" class="btn-primary" wire:click="$wire.openFilterDrawer = false" />
            </div>        
        </div>
    </x-drawer>
  
</div>


