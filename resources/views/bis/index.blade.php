@extends('layouts.app')

@section('content')
<h1 class="title">
        Products overview
    </h1>

    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>
                    Title
                </th>
                <th>
                    Num. variants
                </th>
                <th>
                    Type
                </th>
                <th>
                    Vendor
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allProducts as $product)
                <tr>
                    <td>
                        {{ $loop->iteration }}
                    </td>
                    <td>
                        <a href="https://vivo-life.myshopify.com/admin/products/{{ $product->getId() }}" target="_blank">
                            {{ $product->getTitle() }}
                        </a>
                    </td>
                    <td>
                        {{ count($product->getVariants()) }}
                    </td>
                    <td>
                        {{ $product->getProductType() }}
                    </td>
                    <td>
                        {{ $product->getVendor() }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection