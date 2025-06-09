<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


class TiketController extends Controller
{

    public function download($id)
    {
        try {
            $order = Order::with('user', 'event')->findOrFail($id);
    
            $pdf = Pdf::loadView('pdf.tiket', compact('order'));
            $fileName = 'tiket-'.$order->id.'.pdf';
            $filePath = 'tiket/' . $fileName;
    
            Storage::disk('public')->put($filePath, $pdf->output());

            $url = asset('storage/' . $filePath);
    
            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil dibuat.',
                'download_url' => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tiket: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}

