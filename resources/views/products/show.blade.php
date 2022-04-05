<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($product->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2 mb-6">
            
                @if ($product->image_src)
                    <img src="{{ $product->image_src }}" alt="Image of {{ $product->title }}" />
                @endif
            
                <a class="btn" href="{{ $product->getProductPageUrl() }}" target="_blank">
                    Go to product page
                </a>

                <a class="btn" href="{{ $product->getShopAdminUrl() }}" target="_blank">
                    Go to Shopify admin 
                </a>
                
            </div>

            <h3 class="text-2xl mb-2">
                Variants
            </h3>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left">
                                Title
                            </th>
                            <th>
                                Price
                            </th>
                            <th>
                                SKU
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->variants as $variant)
                            <tr>
                                <td>
                                    <a href="{{ route('products.variants.show', [$product->id, $variant->id]) }}">
                                        {{ $variant->title }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{ $variant->price }}
                                </td>
                                <td class="text-center">
                                    {{ $variant->sku }}
                                </td>
                                <td class="text-right">
                                    <a class="btn" href="{{ $variant->getShopAdminUrl() }}" target="_blank">
                                        Shopify admin 
                                    </a>

                                    <a class="btn" href="{{ route('products.variants.ometria_up', [$product->id, $variant->id]) }}">
                                        Push to Ometria
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>