<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket Event</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 100%; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Tiket Event: {{ $order->event->title }}</h2>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        </div>

        <div class="section">
            <p><span class="label">Nama Event:</span> {{ $order->event->title }}</p>
            <p><span class="label">Lokasi:</span> {{ $order->event->location }}</p>
            <p><span class="label">Tanggal:</span> {{ \Carbon\Carbon::parse($order->event->date)->format('d F Y') }}</p>
            <p><span class="label">Waktu:</span> {{ \Carbon\Carbon::parse($order->event->time)->format('H:i') }}</p>
        </div>

        <div class="section">
            <p><span class="label">Jumlah Pembayaran:</span> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <p><span class="label">Status Pembayaran:</span> {{ $order->paid ? 'Sudah dibayar' : 'Belum dibayar' }}</p>
        </div>

        <div class="section">
            <p><span class="label">Tanggal Pemesanan:</span> {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
