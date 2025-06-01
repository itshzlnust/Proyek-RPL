<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            max-width: 120px;
            margin-bottom: 10px;
        }
        
        .invoice-header h1 {
            color: #8a2be2;
            margin-bottom: 5px;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        
        .invoice-details {
            margin-bottom: 30px;
        }
        
        .row {
            display: flex;
            margin-bottom: 20px;
        }
        
        .col {
            flex: 1;
        }
        
        .col-left {
            padding-right: 20px;
        }
        
        .col-right {
            padding-left: 20px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table th {
            text-align: left;
            width: 35%;
            padding: 5px 0;
            vertical-align: top;
        }
        
        .info-table td {
            padding: 5px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .items-table td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        
        .items-table tfoot td {
            border-top: 2px solid #ddd;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .badge-primary {
            background-color: #8a2be2;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <img src="{{ public_path('images/logo.jpg') }}" alt="Kulitku Skincare Logo" class="logo">
        <h1>Kulitku Skincare</h1>
        <p>Beauty Clinic<br>
        Ruko Mall, Balikpapan Baru AA5 No.36, Damai, Kec. Balikpapan Kota, Kota Balikpapan, Kalimantan Timur 76114<br>
        WhatsApp: +62 813-5168-7891</p>
        <h2 class="invoice-title">INVOICE</h2>
    </div>
    
    <div class="invoice-details">
        <div class="row">
            <div class="col col-left">
                <h3>Bill To:</h3>
                <p>
                    <strong>{{ $order->customer->name }}</strong><br>
                    Customer Code: {{ $order->customer->customer_code }}<br>
                    @if($order->customer->email)
                    Email: {{ $order->customer->email }}<br>
                    @endif
                    @if($order->customer->phone)
                    Phone: {{ $order->customer->phone }}
                    @endif
                </p>
            </div>
            <div class="col col-right">
                <table class="info-table">
                    <tr>
                        <th>Invoice Number:</th>
                        <td>{{ $order->order_code }}</td>
                    </tr>
                    <tr>
                        <th>Invoice Date:</th>
                        <td>{{ $order->order_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>{{ ucfirst($order->status) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th>Treatment</th>
                <th style="width: 15%;">Type</th>
                <th style="width: 10%;">Quantity</th>
                <th style="width: 15%;">Price</th>
                <th style="width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->treatment->name }}</td>
                <td>
                    <span class="badge {{ $item->treatment->is_bundle ? 'badge-info' : 'badge-primary' }}">
                        @if($item->treatment->is_bundle)
                            Bundle: {{ $item->treatment->bundle_name }}
                        @else
                            Individual
                        @endif
                    </span>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">Total:</td>
                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    
    @if($order->notes)
    <div style="margin-top: 20px;">
        <h3>Notes:</h3>
        <p>{{ $order->notes }}</p>
    </div>
    @endif
    
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>For any questions regarding this invoice, please contact our customer service.</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
    </div>
</body>
</html> 