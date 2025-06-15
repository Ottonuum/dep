<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class PayPalController extends Controller
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $domain;

    public function __construct()
    {
        $this->baseUrl = config('services.paypal.mode') === 'sandbox' 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com';
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.secret');
        
        // Get the current domain from the request
        $this->domain = request()->getSchemeAndHttpHost();
    }

    protected function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error('PayPal access token error:', $response->json());
        throw new \Exception('Failed to get PayPal access token');
    }

    public function createOrder(Request $request)
    {
        try {
            $accessToken = $this->getAccessToken();
            $cart = session('cart', []);
            $total = collect($cart)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'USD',
                                'value' => number_format($total, 2, '.', '')
                            ],
                            'description' => 'Pokemon Shop Purchase'
                        ]
                    ],
                    'application_context' => [
                        'return_url' => $this->domain . route('paypal.success', [], false),
                        'cancel_url' => $this->domain . route('shop', [], false),
                        'brand_name' => 'Pokemon Shop',
                        'landing_page' => 'LOGIN',
                        'user_action' => 'PAY_NOW',
                        'shipping_preference' => 'NO_SHIPPING'
                    ]
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            Log::error('PayPal create order error:', $response->json());
            return response()->json(['error' => 'Failed to create PayPal order'], 500);

        } catch (\Exception $e) {
            Log::error('PayPal create order exception:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while creating the order'], 500);
        }
    }

    public function captureOrder(Request $request)
    {
        try {
            $accessToken = $this->getAccessToken();
            $orderId = $request->input('orderId');

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                $orderData = $response->json();
                
                // Create order in database
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total_amount' => $orderData['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                    'status' => 'completed',
                    'payment_method' => 'paypal',
                    'payment_id' => $orderId
                ]);

                // Create order items
                $cart = session('cart', []);
                foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }

                // Clear cart
                session()->forget('cart');

                return response()->json($orderData);
            }

            Log::error('PayPal capture order error:', $response->json());
            return response()->json(['error' => 'Failed to capture PayPal order'], 500);

        } catch (\Exception $e) {
            Log::error('PayPal capture order exception:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while capturing the order'], 500);
        }
    }

    public function success()
    {
        return view('paypal.success', [
            'message' => 'Thank you for your purchase! Your order has been successfully processed.'
        ]);
    }
} 