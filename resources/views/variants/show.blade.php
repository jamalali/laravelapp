<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->title }} | {{ $variant->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('products.show', $product->id) }}">
                <- Back to product
            </a>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
            
                @if ($variant->image_src)
                    <img src="{{ $variant->image_src }}" alt="Image of {{ $variant->title }}" />
                @endif

                <a class="btn" href="{{ $variant->getShopAdminUrl() }}" target="_blank">
                    Go to Shopify admin 
                </a>

                <a class="btn" href="{{ route('products.variants.ometria_up', [$product->id, $variant->id]) }}">
                    Push to Ometria
                </a>
                
            </div>
        </div>
    </div>
</x-app-layout>