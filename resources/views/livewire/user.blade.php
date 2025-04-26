<div>
    <x-modal wire:model="isOpen" title="{{ $user ? 'Edit ' . $user?->name : 'Create User' }}"
        saveButton="{{ $user ? 'update' : 'save' }}" saveLabel="{{ $user ? 'Update User' : 'Create User' }}" closeButton="closeModal">
        <form>
            <x-input-text id="name" name="name" title="Name" placeholder="your name"/>
            <x-input-text type="email" id="email" name="email" title="email" placeholder="name@yourmail"/>
            <label class="mt-3" for="password">Password</label>
            <p class="mb-2 font-normal text-small" id="is-invalid">
                At least 8 characters, have capital letters and special characters.
            </p>
            <div class="relative ms-auto">
                <button type="button" wire:click="passwordToggle" class="absolute z-10 text-base -translate-y-1/2 text-primary end-2 top-1/2">
                    @if ($showPassword)
                        <i class="ri-eye-line"></i>
                    @else
                        <i class="ri-eye-close-line"></i>
                    @endif
                </button>
                <input type="{{ $showPassword ? 'text' : 'password' }}" id="password" class="pe-8 relative form-input {{ $errors->first('password') ? 'border-2 border-danger' : '' }}" wire:model.live="password" placeholder="Input Password">
            </div>
            @error('password')
                <span class="font-normal is-invalid text-danger text-small" id="is-invalid">{{ $message }}</span>
            @enderror
            @if ($roles)
            <div class="flex flex-col gap-3 mt-5">
                <h6>Roles</h6>
                @foreach ($roles as $role)
                    <div class="flex items-center">
                        <input class="rounded form-checkbox text-primary" type="checkbox" id="{{ strtolower($role) }}" value="{{ $role }}" wire:model="role_ids">
                        <label class="ms-1.5" for="{{ strtolower($role) }}">{{ $role }}</label>
                    </div>
                @endforeach
            </div>
            @endif
        </form>
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <button class="text-white btn bg-primary" wire:click="openModal" type="button">
                        Create </button>
                </div>
            </div>
            <div class="flex justify-end mb-4">
                <div class="relative mr-4 ms-auto">
                    <input type="search" class="relative border-none form-input bg-black/5 ps-8" wire:model.live="query"
                        placeholder="Search...">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
                <div class="relative ms-auto">
                    <button data-fc-type="dropdown" data-fc-placement="bottom-end" type="button"
                        class="flex items-center py-2 pl-3 pr-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="mr-2 ri-filter-line"></i>
                    </button>
                    <div class="absolute right-0 z-10 hidden w-56 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            @foreach ($showColumns as $column => $isVisible)
                                <div class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem" tabindex="-1" id="menu-item-0">
                                    <input type="checkbox" class="w-4 h-4 text-indigo-600 form-checkbox"
                                        wire:model.live="showColumns.{{ $column }}">
                                    <label class="block ml-3 text-sm font-medium text-gray-700" for="comments">
                                        {{ ucfirst(str_replace('_', ' ', $column)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @if ($users->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-center text-gray-500">No</th>
                            <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                wire:click="sortByColumn('name')">
                                Name
                                @if ($sortBy === 'name')
                                    @if ($sortDirection === 'asc')
                                        <i class="ri-arrow-up-s-line"></i>
                                    @else
                                        <i class="ri-arrow-down-s-line"></i>
                                    @endif
                                @else
                                    <i class="ri-expand-up-down-line"></i>
                                @endif
                            </th>
                            @if ($showColumns['email'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Email</th>
                            @endif
                            @if ($showColumns['role'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Role</th>
                            @endif
                            @if ($showColumns['created_at'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('created_at')">
                                    Created at
                                    @if ($sortBy === 'created_at')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            @if ($showColumns['updated_at'])
                                <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start"
                                    wire:click="sortByColumn('updated_at')">
                                    Updated at
                                    @if ($sortBy === 'updated_at')
                                        @if ($sortDirection === 'asc')
                                            <i class="ri-arrow-up-s-line"></i>
                                        @else
                                            <i class="ri-arrow-down-s-line"></i>
                                        @endif
                                    @else
                                        <i class="ri-expand-up-down-line"></i>
                                    @endif
                                </th>
                            @endif
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($users as $user)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($users->currentpage() - 1) * $users->perpage() + $loop->index + 1}}
                                </th>
                                <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $user->name }}
                                    @if (auth()->user()->id == $user->id)
                                        <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-danger/10 text-danger">{{ 'You' }}</span>
                                    @endif
                                </td>
                                @if ($showColumns['email'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $user->email }}
                                    </td>
                                @endif
                                @if ($showColumns['role'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        @if ($user->roles?->count() > 0)
                                            @foreach ($user->roles as $role)
                                                @if (strtolower($role->name) == 'admin')
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-danger/10 text-danger">{{ $role->name }}</span>
                                                @elseif (strtolower($role->name) == 'sales')
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-success/10 text-success">{{ $role->name }}</span>
                                                @elseif (strtolower($role->name) == 'user')
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-primary/10 text-primary">{{$role->name}}</span>
                                                @elseif (strtolower($role->name) == 'warehouse')
                                                <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-warning/10 text-warning">{{$role->name}}</span>
                                                @elseif (strtolower($role->name) == 'accounting')
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-1.5 rounded-md text-xs font-medium bg-info/10 text-info">{{$role->name}}</span>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                @endif
                                @if ($showColumns['created_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $user->created_at }}
                                    </td>
                                @endif
                                @if ($showColumns['updated_at'])
                                    <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                        {{ $user->updated_at }}
                                    </td>
                                @endif
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button wire:click="edit({{ $user->id }})"><i
                                                class="ri-edit-circle-line text-primary"></i></button>
                                        @if (auth()->user()->id != $user->id)
                                        <button wire:click="deleteAlert({{ $user->id }})"><i
                                            class="text-base ri-delete-bin-2-line text-danger"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <div class="text-center">
                        <i class="text-4xl ri-file-warning-line"></i>
                        <p class="my-5 text-base">No User Found</p>
                    </div>
                @endif
            </table>
        </div>

        <div class="px-3 py-4">
            <div class="flex justify-between">
                <div class="flex flex-col">
                    <div class="flex items-center">
                        <label class="w-32 text-sm font-medium text-gray-900">Per Page</label>
                        <select
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            wire:model.change="perPage">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
                    </div>
                </div>
                {{ $users->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>
