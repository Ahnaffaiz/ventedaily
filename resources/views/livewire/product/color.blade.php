<div>
    <button class="text-white btn bg-primary" wire:click="openModal" type="button">
        Standard Modal </button>
    <x-modal wire:model="isOpen" title="Custom Modal Title" saveButton="save" closeButton="closeModal">
        <form>
            <x-input-text id="name" name="name" title="Name" />
            <x-textarea-input id="desc" name="desc" title="Description" />
        </form>
    </x-modal>
    <div class="relative mt-4 overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
        <div class="flex items-center justify-between p-4 d">
            <div class="flex">
                <div class="relative w-full">
                    <input type="search" class="relative border-none form-input bg-black/5 ps-8"
                        placeholder="Search...">
                    <span class="absolute z-10 text-base -translate-y-1/2 ri-search-line start-2 top-1/2"></span>
                </div>
            </div>
            <div class="flex space-x-3">
                <div class="flex items-center space-x-3">
                    <label class="w-40 text-sm font-medium text-gray-900">User Type :</label>
                    <select
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="">All</option>
                        <option value="0">User</option>
                        <option value="1">Admin</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">No</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Name</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Desc</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Created at</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Updated at</th>
                        <th scope="col" class="px-4 py-4 text-sm font-medium text-gray-500 text-start">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($colors as $color)
                        <tr class="bg-gray-50 dark:bg-gray-900">
                            <th class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $loop->iteration }}
                            </th>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $color->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $color->desc }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $color->created_at }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-200">
                                {{ $color->updated_at }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-start space-x-3">
                                    <a href="javascript: void(0);"><i class="text-base ri-settings-3-line"></i></a>
                                    <a href="javascript: void(0);"><i class="text-base ri-delete-bin-2-line"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-3 py-4">
            <div class="flex justify-between">
                <div class="flex items-center">
                    <label class="w-32 text-sm font-medium text-gray-900">Per Page</label>
                    <select
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                {{ $colors->links() }}
            </div>
        </div>
    </div>
</div>
