<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * توليد تقرير PDF لطلب شراء محدد
     */
    public function generate(Request $request, int $purchaseRequestId): Response
{
    $purchaseRequest = PurchaseRequest::with([
        // عناصر الطلب + عروض كل عنصر + عناصر كل عرض (إن وُجد)
        'items',
        'items.needsAssessment',
        'items.warehouseCheck',          // لو أردت فحص المخازن لكل عنصر
        'items.estimates',
        'items.estimates.estimateItems', // إذا كان لديك EstimateItem model/details

        // عروض السعر على مستوى الطلب (إن كانت تُخزن مرتبطة بالطلب مباشرة)
        'estimates',

        // علاقات أخرى
        'department',
        'creator',
        'committee',
        'procurements',
        'report',
    ])->findOrFail($purchaseRequestId);

    $filename = "تقرير_طلب_شراء_{$purchaseRequest->request_number}.pdf";

    $pdf = Pdf::loadView('reports.purchase_request', [
        'purchaseRequest' => $purchaseRequest,
        'generatedBy' => auth()->user()->name ?? 'System',
        'generatedAt' => now()->format('Y-m-d H:i:s'),
    ])->setPaper('A4');

    return response()->streamDownload(
        fn() => print($pdf->output()),
        $filename,
        ['Content-Type' => 'application/pdf']
    );
}
}