<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            $cart = session()->get('cart', []);
            
            if (empty($cart)) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
            }

            // Calculate total
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method
            ]);

            // Create order items
            foreach ($cart as $id => $item) {
                $order->items()->create([
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            // Clear the cart
            session()->forget('cart');

            return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            // Clear the cart on failure
            session()->forget('cart');
            
            return redirect()->route('cart.index')->with('error', 'Order failed. Please try again.');
        }
    }
} 