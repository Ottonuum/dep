@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Payment</h2>
                </div>
                <div class="card-body">
                    <form id="payment-form">
                        <div class="mb-3">
                            <label for="card-element" class="form-label">Credit or debit card</label>
                            <div id="card-element" class="form-control">
                                <!-- Stripe Elements will be inserted here -->
                            </div>
                            <div id="card-errors" class="invalid-feedback" role="alert"></div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submit-button">
                            Pay Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid #ccd0d5;
        border-radius: 4px;
        background-color: white;
    }

    .StripeElement--focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .StripeElement--invalid {
        border-color: #dc3545;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ config('stripe.key') }}');
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const displayError = document.getElementById('card-errors');

        // Handle real-time validation errors from the card Element
        card.addEventListener('change', function(event) {
            if (event.error) {
                displayError.textContent = event.error.message;
                displayError.style.display = 'block';
            } else {
                displayError.textContent = '';
                displayError.style.display = 'none';
            }
        });

        // Handle form submission
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            submitButton.disabled = true;

            try {
                // Create PaymentIntent
                const response = await fetch('/payment/create-intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        amount: 1000, // Amount in cents
                        currency: 'usd'
                    })
                });

                const data = await response.json();

                // Confirm the payment
                const result = await stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: {
                            // Add billing details if needed
                        }
                    }
                });

                if (result.error) {
                    displayError.textContent = result.error.message;
                    displayError.style.display = 'block';
                    submitButton.disabled = false;
                } else {
                    // Payment succeeded
                    window.location.href = '/payment/success';
                }
            } catch (error) {
                displayError.textContent = 'An error occurred. Please try again.';
                displayError.style.display = 'block';
                submitButton.disabled = false;
            }
        });
    });
</script>
@endpush
@endsection 