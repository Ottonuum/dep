@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Shopping Cart</h2>
                        @if(count(session('cart', [])) > 0)
                            <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">Clear Cart</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(count(session('cart', [])) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach(session('cart') as $id => $details)
                                        @php
                                            $subtotal = $details['price'] * $details['quantity'];
                                            $total += $subtotal;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $details['image'] }}" alt="{{ $details['name'] }}" style="width: 50px; height: 50px; object-fit: contain;" class="me-3">
                                                    <span>{{ $details['name'] }}</span>
                                                </div>
                                            </td>
                                            <td>${{ number_format($details['price'], 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <div class="input-group" style="width: 120px;">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="decrease">-</button>
                                                        <input type="number" name="quantity" class="form-control form-control-sm text-center" value="{{ $details['quantity'] }}" min="1" max="99">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="increase">+</button>
                                                    </div>
                                                    <button type="submit" class="btn btn-link btn-sm">Update</button>
                                                </form>
                                            </td>
                                            <td>${{ number_format($subtotal, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>${{ number_format($total, 2) }}</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('shop') }}" class="btn btn-secondary">Continue Shopping</a>
                            <a href="{{ route('payment.form') }}" class="btn btn-primary">Proceed to Checkout</a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h3>Your cart is empty</h3>
                            <p class="text-muted">Add some items to your cart to continue shopping.</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Start Shopping</a>
                        </div>
                    @endif
                </div>
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
    .table img {
        border-radius: 4px;
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