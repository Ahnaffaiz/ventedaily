<div>
    <div class="grid gap-6 xl:grid-cols-2 lg:grid-cols-2">
        <div class="col-xl-6 col-lg-12">
            <div class="h-56 overflow-auto card">
                <div class="sticky top-0 z-10 flex items-center justify-between bg-gray-200 card-header dark:bg-gray-800">
                    <h4 class="card-title">Transfer to Store</h4>
                    <button class="inline gap-2 text-white transition-all btn btn-sm bg-primary" wire:click="transferProductAlert('store')">
                        Transfer
                    </button>
                </div>

                <table class="w-full">
                    @if ($transferToStores)
                        <thead
                            class="sticky bg-gray-200 border-b border-gray-100 dark:bg-gray-800 dark:border-b-gray-700 top-12 z-5">
                            <tr>
                                <th class="w-1/3 p-2 text-start">Product</th>
                                <th class="w-1/3 p-2 text-start">Size</th>
                                <th class="w-1/3 p-2 text-start">Color</th>
                                <th class="w-1/3 p-2 text-start">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transferToStores as $keepProduct)
                                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                                    <td class="p-2">{{ $keepProduct['name'] }}</td>
                                    <td class="p-2">{{ $keepProduct['size'] }}</td>
                                    <td class="p-2">{{ $keepProduct['color'] }}</td>
                                    <td class="p-2">{{ $keepProduct['stock'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    @else
                        <div class="my-5 text-center">
                            <i class="text-4xl ri-file-warning-line"></i>
                            <p class="my-5 text-base">No Product Found</p>
                        </div>
                    @endif
                </table>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="h-56 overflow-auto card">
                <div class="sticky top-0 z-10 flex items-center justify-between bg-gray-200 card-header dark:bg-gray-800">
                    <h4 class="card-title">Transfer to Home</h4>
                    <button class="inline gap-2 text-white transition-all btn btn-sm bg-primary" wire:click="transferProductAlert('home')">
                        Transfer
                    </button>
                </div>

                <table class="w-full">
                    @if ($transferToHomes)
                        <thead
                            class="sticky bg-gray-200 border-b border-gray-100 dark:bg-gray-800 dark:border-b-gray-700 top-12 z-5">
                            <tr>
                                <th class="w-1/3 p-2 text-start">Product</th>
                                <th class="w-1/3 p-2 text-start">Size</th>
                                <th class="w-1/3 p-2 text-start">Color</th>
                                <th class="w-1/3 p-2 text-start">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transferToHomes as $keepProduct)
                                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }}">
                                    <td class="p-2">{{ $keepProduct['name'] }}</td>
                                    <td class="p-2">{{ $keepProduct['size'] }}</td>
                                    <td class="p-2">{{ $keepProduct['color'] }}</td>
                                    <td class="p-2">{{ $keepProduct['stock'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    @else
                        <div class="my-5 text-center">
                            <i class="text-4xl ri-file-warning-line"></i>
                            <p class="my-5 text-base">No Product Found</p>
                        </div>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
