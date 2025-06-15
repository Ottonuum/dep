<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $cart = session('cart', []);
            if (empty($cart)) {
                return response()->json(['error' => 'Your cart is empty.'], 400);
            }

            $lineItems = [];
            foreach ($cart as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item['name'],
                        ],
                        'unit_amount' => $item['price'] * 100, // Convert to cents
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success'),
                'cancel_url' => route('stripe.cancel'),
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'user_id' => Auth::id(),
                ],
            ]);

            return response()->json(['id' => $session->id]);
        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing your payment.'], 500);
        }
    }

    public function success()
    {
        try {
            $cart = session('cart', []);
            
            if (empty($cart)) {
                return redirect()->route('products.index')
                    ->with('error', 'No items found in cart.');
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $cart)),
                'status' => 'completed',
                'payment_method' => 'stripe',
            ]);

            // Clear cart
            session()->forget('cart');

            return view('stripe.success', [
                'order' => $order,
                'message' => 'Payment successful! Your order has been placed.'
            ]);
        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            // Clear cart even if order creation fails
            session()->forget('cart');
            return redirect()->route('products.index')
                ->with('error', 'There was an error processing your order. Please try again.');
        }
    }

    public function cancel()
    {
        // Clear cart on cancel
        session()->forget('cart');
        return redirect()->route('products.index')
            ->with('error', 'Payment was cancelled. You can try again.');
    }
} 