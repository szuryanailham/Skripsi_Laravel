<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Membuat order baru

    
    public function index(){
        try {
            $orders = Order::with('user', 'event')->get();
            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve orders.'], 500);
        }
    }

public function store(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'event_id' => 'required|exists:events,id',
        'total_amount' => 'required|numeric|min:0',
    ]);

    // Jika validasi gagal
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Simpan data order
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

    } catch (\Exception $e) {
        // Tangani error tak terduga
        return response()->json([
            'message' => 'Failed to create order',
            'error' => $e->getMessage()
        ], 500);
    }
}

 public function verify($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->status = 'completed';
            $order->save();

            return response()->json(['message' => 'Order verified successfully', 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Order not found or verification failed.'], 404);
        }
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




    public function delete($id)
    {
        try {
            $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $order->delete();

            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Order not found or you do not have access to delete this order.'], 404);
        }
    }

public function uploadProof(Request $request, $id)
{
    // Validasi input
    $validated = $request->validate([
        'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    try {
        $order = Order::findOrFail($id);

        // Simpan file bukti pembayaran
        $file = $request->file('proof_image');
        $path = $file->store('proofs', 'public');

        // Simpan path ke database
        $order->proof_image = $path;
        $order->save();

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diunggah.',
            'order' => $order
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Order tidak ditemukan.',
        ], 404);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat mengunggah bukti.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    
}
