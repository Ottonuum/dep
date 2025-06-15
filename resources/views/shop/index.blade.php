@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>Categories</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('shop') }}" class="list-group-item list-group-item-action {{ !request('category') ? 'active' : '' }}">
                            All Items
                        </a>
                        <a href="{{ route('shop', ['category' => 'pokeballs']) }}" class="list-group-item list-group-item-action {{ request('category') == 'pokeballs' ? 'active' : '' }}">
                            Poké Balls
                        </a>
                        <a href="{{ route('shop', ['category' => 'potions']) }}" class="list-group-item list-group-item-action {{ request('category') == 'potions' ? 'active' : '' }}">
                            Potions
                        </a>
                        <a href="{{ route('shop', ['category' => 'berries']) }}" class="list-group-item list-group-item-action {{ request('category') == 'berries' ? 'active' : '' }}">
                            Berries
                        </a>
                        <a href="{{ route('shop', ['category' => 'tms']) }}" class="list-group-item list-group-item-action {{ request('category') == 'tms' ? 'active' : '' }}">
                            TMs & HMs
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Shop</h2>
                <a href="{{ route('cart') }}" class="cart-icon position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.485-.379L1.91 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    @if(count(session('cart', [])) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7em; padding: 0.3em 0.6em; top: 0; right: -10px; transform: translate(50%, -50%);">
                            {{ array_sum(array_column(session('cart'), 'quantity')) }}
                        </span>
                    @endif
                </a>
            </div>
            <div class="row">
                @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted">{{ $product->description }}</p>
                            <p class="card-text">
                                <strong>Price:</strong> ${{ number_format($product->price, 2) }}
                            </p>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                <div class="input-group me-2" style="width: 120px;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="decrease">-</button>
                                    <input type="number" name="quantity" class="form-control form-control-sm text-center" value="1" min="1" max="99">
                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="increase">+</button>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .quantity-btn {
        width: 30px;
        padding: 0;
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .cart-icon {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.2s;
    }
    .cart-icon:hover {
        color: #0d6efd;
    }
    .badge {
        font-size: 0.75rem;
        transform: translate(-50%, -50%);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity buttons
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const currentValue = parseInt(input.value);
            
            if (this.dataset.action === 'increase' && currentValue < 99) {
                input.value = currentValue + 1;
            } else if (this.dataset.action === 'decrease' && currentValue > 1) {
                input.value = currentValue - 1;
            }
        });
    });

    // Prevent manual input of invalid quantities
    document.querySelectorAll('input[name="quantity"]').forEach(input => {
        input.addEventListener('change', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > 99) {
                this.value = 99;
            }
        });
    });
});
</script>
@endpush
@endsection 