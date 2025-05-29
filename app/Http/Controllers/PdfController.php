<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    //
    public function invoice(int $invoiceId)
    {
        $data = [
            'invoiceId' => $invoiceId,
            'total' => 150.00,
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);

        return $pdf->download("invoice-{$invoiceId}.pdf");
    }
}
