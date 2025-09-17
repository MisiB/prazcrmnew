<div>
    <x-breadcrumbs :items="$breadcrumbs" 
    class="bg-base-300 p-3 mt-2 rounded-box overflow-x-auto whitespace-nowrap"
    link-item-class="text-base" />

    <x-card title="{{ isset($tasks['calendarweek']) && $tasks['calendarweek'] ? $tasks['calendarweek']->week : 'Performance Tracker' }}" subtitle="{{ isset($tasks['calendarweek']) && $tasks['calendarweek'] ? $tasks['calendarweek']->start_date . ' - ' . $tasks['calendarweek']->end_date : 'Select a week to view performance data' }}" class="mt-2 border-2 border-gray-200" separator>
        <x-slot:menu>
            <x-select wire:model.live="currentWeekId" :options="$weeks" option-label="week" option-value="id" placeholder="Filter by week" />
        </x-slot:menu>
        
        <!-- Task Summary -->
        <div class="mb-4 p-4 bg-base-200 rounded-lg">
            <h3 class="text-lg font-semibold mb-2">Performance Summary by Department</h3>
            @if(isset($tasks['users']) && $tasks['users']->count() > 0)
                @php
                    $departmentStats = $this->getDepartmentStats($tasks['users']);
                    $totalTasks = $this->getTotalTasksCount($tasks['users']);
                    $linkedTasks = $this->getLinkedTasksCount($tasks['users']);
                    $unlinkedTasks = $this->getUnlinkedTasksCount($tasks['users']);
                    $linkedPercentage = $this->getLinkedTasksPercentage($tasks['users']);
                @endphp
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Key Metrics Cards -->
                    <div class="space-y-4">
                        <div class="card bg-gradient-to-r from-primary to-primary-focus text-primary-content">
                            <div class="card-body p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-bold">{{ $departmentStats->count() }}</div>
                                        <div class="text-sm opacity-80">Departments</div>
                                    </div>
                                    <div class="text-4xl opacity-60">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-gradient-to-r from-secondary to-secondary-focus text-secondary-content">
                            <div class="card-body p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-bold">{{ $tasks['users']->count() }}</div>
                                        <div class="text-sm opacity-80">Total Users</div>
                                    </div>
                                    <div class="text-4xl opacity-60">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-gradient-to-r from-accent to-accent-focus text-accent-content">
                            <div class="card-body p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-bold">{{ $totalTasks }}</div>
                                        <div class="text-sm opacity-80">Total Tasks</div>
                                    </div>
                                    <div class="text-4xl opacity-60">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Breakdown -->
                    <div class="space-y-4">
                        <div class="card bg-base-100 shadow-sm border">
                            <div class="card-body p-4">
                                <h4 class="card-title text-lg mb-4">Task Breakdown</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 bg-success rounded-full"></div>
                                            <span class="text-sm font-medium">Linked Tasks</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold">{{ $linkedTasks }}</div>
                                            <div class="text-xs text-gray-500">{{ $linkedPercentage }}%</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 bg-warning rounded-full"></div>
                                            <span class="text-sm font-medium">Unlinked Tasks</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold">{{ $unlinkedTasks }}</div>
                                            <div class="text-xs text-gray-500">{{ 100 - $linkedPercentage }}%</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-success h-2 rounded-full" style="width: {{ $linkedPercentage }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>Linked</span>
                                        <span>Unlinked</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="card bg-base-100 shadow-sm border">
                        <div class="card-body p-4">
                            <h4 class="card-title text-lg mb-4">Task Distribution</h4>
                            <div class="h-64 flex items-center justify-center">
                                <x-chart wire:model="myChart" size="sm" />
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <div class="text-gray-500">No data available for the selected week.</div>
                </div>
            @endif
        </div>
        @if(isset($tasks['users']) && $tasks['users']->count() > 0)
            @php
                $departmentStats = $this->getDepartmentStats($tasks['users']);
            @endphp

            <div class="space-y-6">
                @foreach($departmentStats as $department)
                    <div class="card bg-base-100 shadow-sm border">
                        <div class="card-body p-6">
                            <!-- Department Header -->
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-primary">{{ $department['name'] }}</h3>
                                    <div class="grid lg:grid-cols-4 gap-2 mt-2">
                                        <span class="bg-primary text-white px-2 py-1 rounded-md">{{ $department['total_users'] }} users</span>
                                        <span class="bg-secondary text-white px-2 py-1 rounded-md">{{ $department['total_tasks'] }} tasks</span>
                                        <span class="bg-secondary text-white px-2 py-1 rounded-md">{{ $department['linked_tasks'] }} ({{ $department['linked_percentage'] }}%) linked</span>
                                        <span class="bg-warning text-white px-2 py-1 rounded-md">{{ $department['unlinked_tasks'] }} ({{ 100 - $department['linked_percentage'] }}%) unlinked</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Users Table -->
                            <div class="overflow-x-auto">
                                <table class="table table-zebra table-compact">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Tasks (Count & Details)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($department['users'] as $user)
                                            <tr>
                                                <td>
                                                    <div class="flex items-center gap-3">
                                                        <div class="avatar placeholder">
                                                            <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                                <span class="text-xs">{{ substr($user->name, 0, 1) }}{{ substr($user->surname, 0, 1) }}</span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium">{{ $user->name }} {{ $user->surname }}</div>
                                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                  @php
                                                    $actualTasksCount = $this->getUserTaskCount($user);
                                                    $linkedTasksCount = $this->getUserLinkedTasksCount($user);
                                                    $unlinkedTasksCount = $this->getUserUnlinkedTasksCount($user);
                                                    $linkedPercentage = $actualTasksCount > 0 ? round(($linkedTasksCount / $actualTasksCount) * 100, 1) : 0;
                                                  @endphp
                                                  
                                                  @if($actualTasksCount > 0)
                                                    <div class="space-y-2">
                                                      <div class="flex items-center gap-2">
                                                        <span class="badge badge-primary">{{ $actualTasksCount }} task(s)</span>
                                                      </div>
                                                      <div class="text-xs text-gray-600">
                                                        <div class="flex items-center gap-2">
                                                          <span class="badge badge-success badge-sm">{{ $linkedTasksCount }} ({{ $linkedPercentage }}%) linked</span>
                                                          <span class="badge badge-warning badge-sm">{{ $unlinkedTasksCount }} ({{ 100 - $linkedPercentage }}%) unlinked</span>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  @else
                                                    <x-alert class="alert-error">No tasks found</x-alert>
                                                  @endif
                                                </td>
                                                <td>
                                                    <button wire:click="openTaskModal('{{ $user->id }}')" class="btn btn-sm btn-outline btn-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View Tasks
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">No Performance Data Available</h3>
                        <div class="text-xs">Please select a week to view performance data by department.</div>
                    </div>
                </div>
            </div>
        @endif
        
    </x-card>

    <!-- Task Details Modal -->
    @if($showModal && $selectedUser)
    <div class="modal modal-open">
        <div class="modal-box max-w-4xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Task Details - {{ $selectedUser->name }} {{ $selectedUser->surname }}</h3>
                <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost">✕</button>
            </div>
            
            @if($selectedUserTasks->count() > 0)
                @php
                    $tasksByDay = $selectedUserTasks->groupBy(function($task) {
                        return \Carbon\Carbon::parse($task->start_date)->format('l'); // Day name (Monday, Tuesday, etc.)
                    });
                    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                @endphp
                
                <div class="space-y-4">
                    @foreach($daysOfWeek as $day)
                        <div class="card bg-base-100 shadow-sm border">
                            <div class="card-body p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="card-title text-lg">
                                        @if($tasksByDay->has($day))
                                            <span class="text-success">{{ $day }}</span>
                                            <span class="badge badge-success badge-sm">{{ $tasksByDay[$day]->count() }} task(s)</span>
                                        @else
                                            <span class="text-error">{{ $day }}</span>
                                            <span class="badge badge-error badge-sm">No tasks</span>
                                        @endif
                                    </h4>
                                </div>
                                
                                @if($tasksByDay->has($day))
                                    <div class="space-y-3">
                                        @foreach($tasksByDay[$day] as $task)
                                            <div class="bg-base-200 p-3 rounded-lg">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h5 class="font-medium">{{ $task->title }}</h5>
                                                    <div class="flex gap-2">
                                                        <span class="badge badge-outline badge-sm">{{ $task->status }}</span>
                                                        <span class="badge badge-secondary badge-sm">{{ $task->priority }}</span>
                                                        @if($task->individualoutputbreakdown_id)
                                                            <span class="badge badge-success badge-sm">Linked</span>
                                                        @else
                                                            <span class="badge badge-warning badge-sm">Unlinked</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <p class="text-sm text-gray-600 mb-2">{{ $task->description }}</p>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                                    <div>
                                                        <span class="font-medium">Time:</span>
                                                        <span>{{ \Carbon\Carbon::parse($task->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($task->end_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Approval:</span>
                                                        <span class="badge badge-sm">{{ $task->approvalstatus }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-error">
                                        <div class="text-sm">No tasks assigned for this day</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500">No tasks found for this user.</div>
                </div>
            @endif
            
            <div class="modal-action">
                <button wire:click="closeModal" class="btn btn-primary">Close</button>
            </div>
        </div>
    </div>
    @endif
</div> 
 