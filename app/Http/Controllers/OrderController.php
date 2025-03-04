<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Membuat order baru
    public function store(Request $request)
    {
        $order = Order::create([
            'order_number' => 'ORD-' . now()->timestamp,
            'user_id' => 1,
            'event_id' => $request->event_id,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    // Melihat detail order
    public function show($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        return response()->json($order);
    }

    // Melihat semua order user
    public function userOrders()
    {
        $orders = Order::where('user_id', Auth::id())->get();

        return response()->json($orders);
    }

    // Cetak tiket jika status paid
    public function printTicket($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($order->status !== 'paid') {
            return response()->json(['message' => 'Ticket cannot be printed. Order is not paid.'], 400);
        }

        return response()->json([
            'message' => 'Here is your ticket!',
            'ticket' => [
                'order_number' => $order->order_number,
                'event' => $order->event->name,
                'date' => $order->event->date,
                'location' => $order->event->location
            ]
        ]);
    }
}
