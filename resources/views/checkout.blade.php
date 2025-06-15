@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Checkout</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4>Order Summary</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach(session('cart', []) as $id => $item)
                                    @php
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                    @endphp
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>€{{ number_format($item['price'], 2) }}</td>
                                        <td>€{{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>€{{ number_format($total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <form id="payment-form">
                        @csrf
                        <button type="button" id="checkout-button" class="btn btn-primary btn-lg w-100">
                            Pay with Stripe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const checkoutButton = document.getElementById('checkout-button');

        checkoutButton.addEventListener('click', function() {
            // Show loading state
            checkoutButton.disabled = true;
            checkoutButton.textContent = 'Processing...';

            fetch('{{ route('stripe.checkout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function(session) {
                if (session.error) {
                    throw new Error(session.error);
                }
                return stripe.redirectToCheckout({ sessionId: session.id });
            })
            .then(function(result) {
                if (result.error) {
                    throw new Error(result.error.message);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
                checkoutButton.disabled = false;
                checkoutButton.textContent = 'Pay with Stripe';
            });
        });
    });
</script>
@endsection 