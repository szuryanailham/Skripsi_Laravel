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
            'user_id' => Auth::id(),
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
        try {
            $order = Order::where('id', $id)->where('user_id', Auth::id())->with('event')->firstOrFail();
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Order not found or you do not have access to this order.'], 404);
        }
    }

    // Melihat semua order user
    public function userOrders()
    {
        try {
            $orders = Order::where('user_id', Auth::id())->get();
            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve orders.'], 500);
        }
    }

    // Cetak tiket jika status paid
    public function printTicket($order_number)
    {
        $order = Order::where('order_number', $order_number)
            ->where('user_id', Auth::id())
            ->with('event') // Pastikan relasi event dimuat
            ->firstOrFail();
    
        if ($order->status !== 'completed') {
            return response()->json(['message' => 'Ticket cannot be printed. Order is not paid.'], 400);
        }
    
        if (!$order->event) {
            return response()->json(['message' => 'Event data not found.'], 404);
        }
    
        return response()->json([
            'message' => 'Here is your ticket!',
            'ticket' => [
                'order_number' => $order->order_number,
                'event' => [
                    'name' => $order->event->name,
                    'date' => $order->event->date,
                    'location' => $order->event->location,
                    'description' => $order->event->description ?? 'No description available',
                ],
                'user_name' => $order->user->name,
                'total_amount' => $order->total_amount,
                'status' => $order->status
            ]
        ]);
    }
    
}
