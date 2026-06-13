<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Return #{{ $purchaseReturn->Ref }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 15px; }
        h2, h4 { margin: 0 0 10px 0; }
        hr { border: none; border-top: 1px solid #ddd; }
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .justify-content-end { justify-content: flex-end; }
        .row { display: flex; flex-wrap: wrap; }
        .col-md-3 { width: 25%; box-sizing: border-box; padding: 0 8px; }
        .col-md-5 { width: 41.666%; box-sizing: border-box; padding: 0 8px; }
        .bg-light { background: #f9f9f9; }
        .p-4 { padding: 16px; }
        .p-5 { padding: 20px; }
        .rounded-3 { border-radius: 8px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 10px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-primary { background: #cce5ff; color: #004085; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .mt-4 { margin-top: 20px; }
        .info-box { background: #f8f9fa; border-radius: 6px; padding: 12px 16px; margin-bottom: 8px; }
        .info-label { font-size: 10px; color: #888; }
        .info-value { font-size: 14px; font-weight: bold; color: #1e1e1e; }
    </style>
</head>
<body>
    <h2>Purchase Return Invoice</h2>
    <p><strong>Ref:</strong> {{ $purchaseReturn->Ref }} | <strong>Date:</strong> {{ $purchaseReturn->date }}</p>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box">
                <div class="info-label">Supplier</div>
                <div class="info-value">{{ $purchaseReturn->provider ? $purchaseReturn->provider->name : '-' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <div class="info-label">Warehouse</div>
                <div class="info-value">{{ $purchaseReturn->warehouse ? $purchaseReturn->warehouse->name : '-' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge {{ $purchaseReturn->status == 'received' ? 'badge-success' : ($purchaseReturn->status == 'ordered' ? 'badge-primary' : 'badge-warning') }}">
                        {{ $purchaseReturn->status }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <div class="info-label">Payment</div>
                <div class="info-value">
                    <span class="badge {{ $purchaseReturn->payment_status == 'paid' ? 'badge-success' : ($purchaseReturn->payment_status == 'partial' ? 'badge-primary' : 'badge-warning') }}">
                        {{ $purchaseReturn->payment_status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <h4>Products ({{ count($details) }} items)</h4>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Code</th>
                <th class="text-end">Cost</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Tax</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $idx => $detail)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $detail['name'] }}</td>
                <td>{{ $detail['code'] }}</td>
                <td class="text-end">{{ number_format($detail['cost'], 2) }}</td>
                <td class="text-end">{{ $detail['quantity'] }} {{ $detail['unit'] ?? '' }}</td>
                <td class="text-end">{{ number_format($detail['discount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($detail['TaxNet'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($detail['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row justify-content-end">
        <div class="col-md-5">
            <div class="bg-light p-4 rounded-3">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span class="fw-bold">{{ number_format($subtotal, 2) }}</span>
                </div>
                @if($purchaseReturn->discount > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount</span>
                    <span class="text-danger">- {{ number_format($purchaseReturn->discount, 2) }}</span>
                </div>
                @endif
                @if($purchaseReturn->shipping > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping</span>
                    <span>+ {{ number_format($purchaseReturn->shipping, 2) }}</span>
                </div>
                @endif
                @if($purchaseReturn->TaxNet > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax ({{ $purchaseReturn->tax_rate }}%)</span>
                    <span>+ {{ number_format($purchaseReturn->TaxNet, 2) }}</span>
                </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">Grand Total</span>
                    <span class="fw-bold">{{ number_format($purchaseReturn->GrandTotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Paid</span>
                    <span class="text-success">{{ number_format($purchaseReturn->paid_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Due</span>
                    <span class="fw-bold {{ ($purchaseReturn->GrandTotal - $purchaseReturn->paid_amount) > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($purchaseReturn->GrandTotal - $purchaseReturn->paid_amount, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($purchaseReturn->notes)
    <div class="mt-4">
        <h4>Notes</h4>
        <p>{{ $purchaseReturn->notes }}</p>
    </div>
    @endif
</body>
</html>
