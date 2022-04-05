<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">

                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left">
                                Product
                            </th>
                            <th>
                                Variant
                            </th>
                            <th>
                                Product ID
                            </th>
                            <th>
                                Variant ID
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    {{ $product['title'] }}
                                </td>
                                <td></td>
                                <td>
                                    {{ $product['id'] }}
                                </td>
                                <td></td>
                            </tr>
                            @foreach ($product['variants'] as $variant)
                                <tr>
                                    <td></td>
                                    <td>
                                        {{ $variant['title'] }}
                                    </td>
                                    <td></td>
                                    <td>
                                        {{ $variant['id'] }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4" class="bg-gray-300"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>

        </div>
    </div>
</x-app-layout>