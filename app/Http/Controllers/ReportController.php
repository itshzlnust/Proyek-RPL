<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function generateInvoice(Order $order)
    {
        $order->load(['customer', 'orderItems.treatment']);
        
        // Set paper size and orientation
        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('a4', 'portrait');
        
        // Load the view with data
        $pdf->loadView('orders.invoice', compact('order'));
        
        // Return the PDF for download
        return $pdf->download('invoice-' . $order->order_code . '.pdf');
    }
} 