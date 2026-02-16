<h3>تقرير المخازن</h3>
<table>
    <tr>
        <th>المادة</th>
        <th>التوفر</th>
        <th>الكمية</th>
        <th>الحالة</th>
        <th>التوصية</th>
        <th>ملاحظات</th>
    </tr>
    @foreach($purchaseRequest->items as $item)
    @php $wc = $item->warehouseCheck @endphp
    <tr>
        <td>{{ $item->requestItem->item_name }}</td>
        <td>{{ $wc?->availability == 'available' ? 'متوفر' : 'غير متوفر' }}</td>
        <td>{{ $wc?->available_quantity ?? '-' }}</td>
        <td>{{ $wc?->condition ?? '-' }}</td>
        <td>
            @if($wc?->recommendation == 'provide_from_stock') من المخزون
            @elseif($wc?->recommendation == 'purchase_new') شراء جديد
            @else رفض
            @endif
        </td>
        <td>{{ $wc?->notes ?? '-' }}</td>
    </tr>
    @endforeach
</table>

<h3>بيان الحاجة</h3>
<table>
    <tr>
        <th>المادة</th>
        <th>الحالة</th>
        <th>المواصفات المعدلة</th>
        <th>السبب</th>
    </tr>
    @foreach($purchaseRequest->items as $item)
    @php $na = $item->needsAssessment @endphp
    <tr>
        <td>{{ $item->requestItem->item_name }}</td>
        <td>
            @if($na?->needs_status == 'needed') مطلوب
            @elseif($na?->needs_status == 'not_needed') غير مطلوب
            @else معدل
            @endif
        </td>
        <td>{{ $na?->modified_specifications ?? '-' }}</td>
        <td>{{ $na?->reason ?? '-' }}</td>
    </tr>
    @endforeach
</table>