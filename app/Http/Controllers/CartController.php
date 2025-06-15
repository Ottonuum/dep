<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('shop.cart', compact('cart', 'total'));
    }

    public function add(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        // Here you would typically fetch the product from a database
        $allProducts = [
            [
                'id' => 1,
                'name' => 'Pokeball',
                'price' => 200,
                'image' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png',
                'category' => 'balls'
            ],
            [
                'id' => 2,
                'name' => 'Great Ball',
                'price' => 600,
                'image' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/great-ball.png',
                'category' => 'balls'
            ],
            [
                'id' => 3,
                'name' => 'Potion',
                'price' => 300,
                'image' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/potion.png',
                'category' => 'healing'
            ]
        ];

        $product = collect($allProducts)->firstWhere('id', $id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found!');
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        if ($request->has('id') && $request->has('quantity')) {
            $cart = session()->get('cart', []);
            $id = $request->input('id');
            $quantity = $request->input('quantity');

            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
                session()->put('cart', $cart);
            }
        }

        return redirect()->back()->with('success', 'Cart updated successfully!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product removed from cart successfully!');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Cart cleared successfully!');
    }
}
