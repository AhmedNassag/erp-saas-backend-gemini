<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Purchase #{{ $purchase->Ref }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; }
  h2 { text-align: center; margin-bottom: 20px; color: #1e1e1e; }
  .info { margin-bottom: 20px; }
  .info table { width: 100%; border-collapse: collapse; }
  .info td { padding: 4px 8px; }
  .info td:first-child { font-weight: bold; width: 100px; }
  table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
  table.items th, table.items td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
  table.items th { background: #f5f5f5; font-weight: bold; }
  table.items td.text-end { text-align: right; }
  .totals { width: 300px; margin-left: auto; }
  .totals table { width: 100%; }
  .totals td { padding: 4px 8px; }
  .totals td:last-child { text-align: right; font-weight: bold; }
  .totals .grand { font-size: 16px; }
  hr { border: none; border-top: 1px solid #ddd; }
  .footer { text-align: center; margin-top: 30px; color: #888; font-size: 10px; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
  .badge-success { background: #d4edda; color: #155724; }
  .badge-primary { background: #cce5ff; color: #004085; }
  .badge-warning { background: #fff3cd; color: #856404; }
  .badge-danger { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>
  <h2>Purchase Invoice</h2>

  <div class="info">
    <table>
      <tr><td>Reference</td><td>{{ $purchase->Ref }}</td></tr>
      <tr><td>Date</td><td>{{ $purchase->date }}</td></tr>
      <tr><td>Supplier</td><td>{{ $purchase->provider->name ?? 'N/A' }}</td></tr>
      <tr><td>Warehouse</td><td>{{ $purchase->warehouse->name ?? 'N/A' }}</td></tr>
      <tr><td>Status</td><td>{{ $purchase->status }}</td></tr>
      <tr><td>Payment</td><td>{{ $purchase->payment_status }}</td></tr>
    </table>
  </div>

  <table class="items">
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
      @foreach ($details as $i => $d)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $d['name'] ?? '' }}</td>
        <td>{{ $d['code'] ?? '' }}</td>
        <td class="text-end">{{ $d['cost'] ?? 0 }}</td>
        <td class="text-end">{{ ($d['quantity'] ?? 0) . ' ' . ($d['unit'] ?? '') }}</td>
        <td class="text-end">{{ $d['discount'] ?? 0 }}</td>
        <td class="text-end">{{ $d['TaxNet'] ?? 0 }}</td>
        <td class="text-end">{{ $d['total'] ?? 0 }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="totals">
    <table>
      <tr><td>Subtotal</td><td>{{ $subtotal }}</td></tr>
      @if ($purchase->discount > 0)
      <tr><td>Discount</td><td>- {{ $purchase->discount }}</td></tr>
      @endif
      @if ($purchase->shipping > 0)
      <tr><td>Shipping</td><td>+ {{ $purchase->shipping }}</td></tr>
      @endif
      @if ($purchase->TaxNet > 0)
      <tr><td>Tax ({{ $purchase->tax_rate }}%)</td><td>+ {{ $purchase->TaxNet }}</td></tr>
      @endif
      <tr><td colspan="2"><hr></td></tr>
      <tr class="grand"><td>Grand Total</td><td>{{ $purchase->GrandTotal }}</td></tr>
      <tr><td>Paid</td><td>{{ $purchase->paid_amount }}</td></tr>
      <tr><td>Due</td><td>{{ $purchase->GrandTotal - $purchase->paid_amount }}</td></tr>
    </table>
  </div>

  @if ($purchase->notes)
  <div style="margin-top: 20px;">
    <strong>Notes:</strong>
    <p>{{ $purchase->notes }}</p>
  </div>
  @endif

  <div class="footer">Generated on {{ date('Y-m-d H:i:s') }}</div>
</body>
</html>
