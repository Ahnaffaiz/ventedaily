<div>
    <x-modal wire:model="isOpen" title="{{ $role ? 'Edit ' . $role?->name : 'Create User' }}"
        saveButton="{{ $role ? 'update' : 'save' }}" saveLabel="{{ $role ? 'Update User' : 'Create User' }}" closeButton="closeModal" large="true">
        <form>
            <x-input-text id="name" name="name" title="Name" placeholder="your name" disabled/>
            @if ($permissions)
            <div class="flex flex-col gap-3 mt-5">
                <h6>Permission</h6>
                <div class="grid grid-cols-4 gap-4">
                    @foreach ($permissions as $permission)
                        <div class="flex items-center">
                            <input class="rounded form-checkbox text-primary" type="checkbox" id="{{ strtolower($permission) }}" value="{{ $permission }}" wire:model="permission_ids">
                            <label class="ms-1.5" for="{{ strtolower($permission) }}">{{ $permission }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </form>
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @if ($roles->count() > 0)
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
                            <th scope="col" class="justify-end px-4 py-4 pr-3 text-sm font-medium text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($roles as $role)
                            <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-100 dark:bg-gray-900' : '' }}">
                                <th class="w-2/12 px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{($roles->currentpage() - 1) * $roles->perpage() + $loop->index + 1}}
                                </th>
                                <td class="w-2/12 px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                    {{ $role->name }}
                                </td>
                                <td class="w-2/12 px-4 py-4">
                                    <div class="flex items-center justify-center space-x-3">
                                        <button wire:click="edit({{ $role->id }})"><i
                                                class="ri-edit-circle-line text-primary"></i></button>
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
                {{ $roles->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>
