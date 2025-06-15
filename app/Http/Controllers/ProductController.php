<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    private function getMockProducts()
    {
        return [
            [
                'id' => 1,
                'name' => 'Laravel T-Shirt',
                'description' => 'A comfortable t-shirt featuring the Laravel logo. Made from 100% cotton.',
                'price' => 29.99,
                'stock_quantity' => 50,
                'image' => 'https://files.catbox.moe/gsrzs8.png',
            ],
            [
                'id' => 2,
                'name' => 'Laravel Mug',
                'description' => 'A ceramic mug with the Laravel logo. Perfect for your morning coffee.',
                'price' => 19.99,
                'stock_quantity' => 30,
                'image' => 'https://files.catbox.moe/fd6jdt.png',
            ],
            [
                'id' => 3,
                'name' => 'Laravel Sticker Pack',
                'description' => 'A set of high-quality stickers featuring Laravel-related designs.',
                'price' => 9.99,
                'stock_quantity' => 100,
                'image' => 'https://files.catbox.moe/pxdyt7.png',
            ],
            [
                'id' => 4,
                'name' => 'Laravel Hoodie',
                'description' => 'A warm and cozy hoodie with the Laravel logo. Perfect for coding sessions.',
                'price' => 49.99,
                'stock_quantity' => 25,
                'image' => 'https://files.catbox.moe/ov05g2.png',
            ],
            [
                'id' => 5,
                'name' => 'Laravel Notebook',
                'description' => 'A premium notebook with the Laravel logo. Great for planning and note-taking.',
                'price' => 14.99,
                'stock_quantity' => 40,
                'image' => 'https://files.catbox.moe/tagxq7.png',
            ],
        ];
    }

    public function index()
    {
        $products = Cache::remember('products', 3600, function () {
            return [
                [
                    'id' => 1,
                    'name' => 'Laravel T-Shirt',
                    'description' => 'A comfortable t-shirt featuring the Laravel logo. Made from 100% cotton.',
                    'price' => 29.99,
                    'stock_quantity' => 50,
                    'image' => "https://files.catbox.moe/gsrzs8.png",
                ],
                [
                    'id' => 2,
                    'name' => 'Laravel Mug',
                    'description' => 'A ceramic mug with the Laravel logo. Perfect for your morning coffee.',
                    'price' => 19.99,
                    'stock_quantity' => 30,
                    'image' => "https://files.catbox.moe/fd6jdt.png",
                ],
                [
                    'id' => 3,
                    'name' => 'Laravel Sticker Pack',
                    'description' => 'A set of high-quality stickers featuring Laravel-related designs.',
                    'price' => 9.99,
                    'stock_quantity' => 100,
                    'image' => "https://files.catbox.moe/pxdyt7.png",
                ],
                [
                    'id' => 4,
                    'name' => 'Laravel Hoodie',
                    'description' => 'A warm and cozy hoodie with the Laravel logo. Perfect for coding sessions.',
                    'price' => 49.99,
                    'stock_quantity' => 25,
                    'image' => "https://files.catbox.moe/ov05g2.png",
                ],
                [
                    'id' => 5,
                    'name' => 'Laravel Notebook',
                    'description' => 'A premium notebook with the Laravel logo. Great for planning and note-taking.',
                    'price' => 14.99,
                    'stock_quantity' => 40,
                    'image' => "https://files.catbox.moe/tagxq7.png",
                ],
            ];
        });

        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $products = Cache::get('products', []);
        $product = collect($products)->firstWhere('id', $id);

        if (!$product) {
            abort(404);
        }

        return view('products.show', compact('product'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('products', 'public');
            $validated['image'] = $path;
        }

        Product::create($validated);
        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $path = $image->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);
        return redirect()->route('products.show', $product);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        return redirect()->route('products.index');
    }
}
