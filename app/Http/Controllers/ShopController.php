<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    private $products = [
        [
            'id' => 1,
            'name' => 'Poké Ball',
            'description' => 'A device for catching wild Pokémon. It\'s thrown like a ball at the target. It is designed as a capsule system.',
            'price' => 200,
            'category' => 'pokeballs',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png'
        ],
        [
            'id' => 2,
            'name' => 'Great Ball',
            'description' => 'A high-performance Ball that offers a higher Pokémon catch rate than a standard Poké Ball.',
            'price' => 600,
            'category' => 'pokeballs',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/great-ball.png'
        ],
        [
            'id' => 3,
            'name' => 'Ultra Ball',
            'description' => 'An ultra-performance Ball that offers a higher Pokémon catch rate than a Great Ball.',
            'price' => 1200,
            'category' => 'pokeballs',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/ultra-ball.png'
        ],
        [
            'id' => 4,
            'name' => 'Potion',
            'description' => 'A spray-type medicine for treating wounds. It can be used to restore 20 HP to an injured Pokémon.',
            'price' => 300,
            'category' => 'potions',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/potion.png'
        ],
        [
            'id' => 5,
            'name' => 'Super Potion',
            'description' => 'A spray-type medicine for treating wounds. It can be used to restore 50 HP to an injured Pokémon.',
            'price' => 700,
            'category' => 'potions',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/super-potion.png'
        ],
        [
            'id' => 6,
            'name' => 'Hyper Potion',
            'description' => 'A spray-type medicine for treating wounds. It can be used to restore 200 HP to an injured Pokémon.',
            'price' => 1500,
            'category' => 'potions',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/hyper-potion.png'
        ],
        [
            'id' => 7,
            'name' => 'Oran Berry',
            'description' => 'A Berry to be consumed by Pokémon. A Pokémon holding it can restore 10 HP in battle.',
            'price' => 100,
            'category' => 'berries',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/oran-berry.png'
        ],
        [
            'id' => 8,
            'name' => 'Sitrus Berry',
            'description' => 'A Berry to be consumed by Pokémon. A Pokémon holding it can restore 30 HP in battle.',
            'price' => 300,
            'category' => 'berries',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/sitrus-berry.png'
        ],
        [
            'id' => 9,
            'name' => 'TM01',
            'description' => 'Teaches a Pokémon a move. Contains Focus Punch, a powerful Fighting-type move.',
            'price' => 3000,
            'category' => 'tms',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/tm-normal.png'
        ],
        [
            'id' => 10,
            'name' => 'TM02',
            'description' => 'Teaches a Pokémon a move. Contains Dragon Claw, a powerful Dragon-type move.',
            'price' => 3000,
            'category' => 'tms',
            'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/tm-normal.png'
        ]
    ];

    public function index(Request $request)
    {
        $category = $request->get('category');
        $products = collect($this->products);

        if ($category) {
            $products = $products->where('category', $category);
        }

        return view('shop.index', [
            'products' => $products->map(function ($product) {
                return (object) $product;
            })
        ]);
    }

    public function show($id)
    {
        $product = collect($this->products)->firstWhere('id', $id);
        
        if (!$product) {
            abort(404);
        }

        return view('shop.show', [
            'product' => (object) $product
        ]);
    }

    public function addToCart(Request $request, $id)
    {
        $product = collect($this->products)->firstWhere('id', $id);
        
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $quantity = $request->input('quantity', 1);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'name' => $product['name'],
                'quantity' => $quantity,
                'price' => $product['price'],
                'image' => $product['image_url']
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Cart updated successfully!');
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Product removed from cart successfully!');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Cart cleared successfully!');
    }
} 