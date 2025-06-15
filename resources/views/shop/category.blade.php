@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-shopping-cart"></i> {{ ucfirst($category) }} Items
                        </h4>
                        <div>
                            <a href="{{ route('cart') }}" class="btn btn-outline-primary me-2 position-relative">
                                <i class="fas fa-shopping-cart"></i> View Cart
                                @if(count(session('cart', [])) > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6em; padding: 0.4em 0.7em; top: 5px; right: -10px;">
                                        {{ array_sum(array_column(session('cart'), 'quantity')) }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Back to Shop
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="{{ $product['image'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product['name'] }}</h5>
                                        <p class="card-text text-primary">{{ $product['price'] }} PokeCoins</p>
                                        <p class="card-text">{{ $product['description'] ?? 'A useful item for your Pokemon journey.' }}</p>
                                        
                                        <form action="{{ route('cart.add', $product['id']) }}" method="POST" class="mb-3">
                                            @csrf
                                            <div class="input-group mb-3">
                                                <input type="number" name="quantity" class="form-control" value="1" min="1" max="99">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                                </button>
                                            </div>
                                        </form>

                                        @if(session('role') === 'admin')
                                            <div class="btn-group w-100">
                                                <a href="{{ route('shop.product.edit', $product['id']) }}" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('shop.product.destroy', $product['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 