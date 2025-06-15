@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Shopping Cart</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(count(session('cart', [])) > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
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
                                        <td>
                                            @if(isset($item['image']))
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            {{ $item['name'] }}
                                        </td>
                                        <td>€{{ number_format($item['price'], 2) }}</td>
                                        <td>
                                            <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm d-inline-block" style="width: 70px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                            </form>
                                        </td>
                                        <td>€{{ number_format($subtotal, 2) }}</td>
                                        <td>
                                            <form action="{{ route('cart.remove', $id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>€{{ number_format($total, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Continue Shopping</a>
                            <a href="{{ route('cart.checkout') }}" class="btn btn-primary">Proceed to Checkout</a>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Your cart is empty.
                        </div>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 