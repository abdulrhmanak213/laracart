<?php

namespace abdulrhmanak\laracart\Http\Models;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_discount',
    ];

    public function cart_products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartProduct::class);
    }

    public function show($id)
    {
        $user = User::query()->find($id);
        $cart = $this->getCart($user, $id);
        $cart = $this->getCartData($cart);
        return $cart;
    }

    public function getCart($user, $id)
    {
        if ($user) {
            $cart = Cart::query()->with('cart_products.product')->firstOrCreate(['user_id' => $user->id]);
        }
        elseif ($id) {
            $cart = Cart::query()->with('cart_products.product')->firstOrCreate(['session_id' => $id]);
        }
        else {
            abort(400, 'Please use authenticated user or send session_id!');
        }
        return $cart;
    }

    public function getCartData($cart)
    {
        $cart['invoice_value'] = 0;
        $cart['tax'] = 0;
        $cart['shipment_fees'] = 0;
        $card_products = $cart->cart_products;
        foreach ($card_products as $card_product) {
            $cart['invoice_value'] += $card_product->quantity * $card_product->product->price;
        }
        $cart['total'] = ($cart['invoice_value'] - $cart['invoice_value'] * ($cart->coupon_discount / 100)) + $cart['tax'] + $cart['shipment_fees'];
        return $cart;
    }

    public function addProduct($product_id, $id, $quantity, $note)//: \Illuminate\Http\Response
    {
        $user = User::query()->find($id);
        $cart = $this->getCart($user, $id);
        $product = Product::query()->findOrFail($product_id);
        $product_cart = CartProduct::query()->firstOrNew(['product_id' => $product->id, 'cart_id' => $cart->id]);
        $product_cart->note = $note;
        $product_cart->quantity += $quantity;
        $product_cart->save();
        return self::success('Product added to cart successfully!');
    }

    public function removeProduct($product_id, $id): \Illuminate\Http\Response
    {
        $user = User::query()->find($id);
        $cart = $this->getCart($user, $id);
        $product = Product::query()->findOrFail($product_id);
        $product_cart = CartProduct::query()->where(['product_id' => $product->id, 'cart_id' => $cart->id])->firstOrFail();
        $product_cart->delete();
        return self::success('Product removed from cart successfully!');
    }

    public function decreaseProduct($product_id, $id): \Illuminate\Http\Response
    {
        $user = User::query()->find($id);
        $cart = $this->getCart($user, $id);
        $product = Product::query()->findOrFail($product_id);
        $product_cart = CartProduct::query()->where(['product_id' => $product->id, 'cart_id' => $cart->id])->firstOrFail();
        if ($product_cart->quantity > 1) {
            $product_cart->quantity--;
            $product_cart->save();
        }
        else {
            $product_cart->delete();
        }
        return self::success('Product decreased from cart successfully!');
    }

}
