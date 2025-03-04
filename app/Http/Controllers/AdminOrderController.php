<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Melihat semua order masuk
    public function index()
    {
        return response()->json(Order::all());
    }

    // Menghapus order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

    // Mengubah status order (pending → paid/failed)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;

        if ($request->status == 'paid') {
            $order->paid = true;
            $order->paid_at = now();
        } else {
            $order->paid = false;
            $order->paid_at = null;
        }

        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }
}
