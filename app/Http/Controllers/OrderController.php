<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use App\Models\Cart;

class OrderController extends Controller
{
    public function index()
    {
        return view('order.index');
    }
    public function test()
    {
        $order = new Order();

        $order->order_number = rand(200, 299) . '' . Carbon::now()->timestamp;
        $order->quantity = 1;
        $order->product_id = 1;
        $order->price = 2000;
        $order->save();

        dd($order->date);
    }
    public function store(Request $request)
    {
        $cart = Cart::findOrFail($request->cart_id)->with('product')->first();
        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->product_id = $cart->product->id;
        $order->quantity = $request->quantity;

        //shipping cost defult 100rs
        $order->shipping_cost = 100;
        $order->order_number = rand(200, 299) . '' . Carbon::now()->timestamp;


        if ($cart->product->onSale) {
            $totalPrice = $cart->product->sale_price;
        } else {
            $totalPrice = $cart->product->price;
        }
        $order->price = $totalPrice * $request->quantity;
        $order->save();
        return redirect(route('user.my-order'));
    }
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $orders = Order::whereIn('id', $ids)->get(['id', 'status']);
        foreach ($orders as $order) {
            $order->delete();
        }
        Alert::toast('Order removed from the database!', 'success');
        return redirect(route('shipCancelled.index'));
    }
}
