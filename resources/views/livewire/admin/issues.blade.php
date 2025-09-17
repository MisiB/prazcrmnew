<div>
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <x-card>
              <div class="flex items-center space-x-2 justify-left">
                <x-icon name="o-clock"/>
                <div class="text-xl font-bold">Pending Tasks</div>
              </div>
              <hr/>
              <div class="rounded-lg border border-gray-200 p-2 mt-2">
                <div class="text-sm font-bold">Task title</div>
                <div class="text-sm text-gray-600">{{ Str::limit('Task description', 5) }}</div>
               <div class="flex justify-left space-x-2 mt-2">
                <x-badge value="High" class="badge-error badge-xs" />
                <x-badge value="Pending" class="badge-soft badge-xs" />
                <x-badge value="Pending" class="badge-soft badge-xs" />
          
               </div>
               <div class="text-sm text-gray-600 mt-2"> Complete on: 2025-06-17</div>
               <div class="text-sm text-gray-600 mt-2"> Approval status: <span class="font-bold font-yellow">Pending</span></div>
    
               <div class="text-sm text-gray-600 mt-2"> # 123456</div>
            </div>
            </x-card>
            <x-card>
              <div class="flex items-center space-x-2 justify-left">
                <x-icon name="o-clock"/>
                <div class="text-xl font-bold">Ongoing Tasks</div>
              </div>
              <hr/>
              <div class="rounded-lg border border-gray-200 p-2 mt-2">
                <div class="text-sm font-bold">Task title</div>
                <div class="text-sm text-gray-600">{{ Str::limit('Task description', 5) }}</div>
               <div class="flex justify-left space-x-2 mt-2">
                <x-badge value="High" class="badge-error badge-xs" />
                <x-badge value="Pending" class="badge-soft badge-xs" />
                <x-badge value="Pending" class="badge-soft badge-xs" />
          
               </div>
               <div class="text-sm text-gray-600 mt-2"> Complete on: 2025-06-17</div>
               <div class="text-sm text-gray-600 mt-2"> Approval status: <span class="font-bold font-yellow">Pending</span></div>
    
               <div class="text-sm text-gray-600 mt-2"> # 123456</div>
            </div>
            </x-card>
            <x-card>
              <div class="flex items-center space-x-2 justify-left">
                <x-icon name="o-clock"/>
                <div class="text-xl font-bold">Completed Tasks</div>
              </div>
              <hr/>
              <div class="rounded-lg border border-gray-200 p-2 mt-2">
                <div class="text-sm font-bold">Task title</div>
                <div class="text-sm text-gray-600">{{ Str::limit('Task description', 5) }}</div>
               <div class="flex justify-left space-x-2 mt-2">
                <x-badge value="High" class="badge-error badge-xs" />
                <x-badge value="Pending" class="badge-soft badge-xs" />
                <x-badge value="Pending" class="badge-soft badge-xs" />
          
               </div>
               <div class="text-sm text-gray-600 mt-2"> Complete on: 2025-06-17</div>
               <div class="text-sm text-gray-600 mt-2"> Approval status: <span class="font-bold font-yellow">Pending</span></div>
    
               <div class="text-sm text-gray-600 mt-2"> # 123456</div>
            </div>
            </x-card>
          
        </div>
      </div>
</div>
