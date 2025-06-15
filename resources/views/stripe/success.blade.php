@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle text-success"></i> Payment Successful
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h3>Thank You for Your Purchase!</h3>
                        <p class="text-muted">Your order has been successfully placed.</p>
                    </div>

                    <div class="alert alert-info">
                        <h5>Order Details</h5>
                        <p class="mb-1">Order Total: {{ $order->total_amount }} PokeCoins</p>
                        <p class="mb-1">Order Status: <span class="badge bg-success">{{ ucfirst($order->status) }}</span></p>
                        <p class="mb-0">Payment Method: {{ ucfirst($order->payment_method) }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('shop') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 