<?php

namespace Modules\Inventory\Repositories\PaymentPurchase;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\PaymentPurchase\PaymentPurchase;
use Modules\Inventory\Repositories\PaymentPurchase\PaymentPurchaseInterface;
use Modules\Inventory\Resources\PaymentPurchase\PaymentPurchaseResource;
use Modules\Inventory\Models\Purchase\Purchase;

class PaymentPurchaseRepository extends BaseRepository implements PaymentPurchaseInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new PaymentPurchase();
    }

    protected function getResourceClass(): string
    {
        return PaymentPurchaseResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Payment Purchases';
    }

    protected function getSingularName(): string
    {
        return 'Payment Purchase';
    }

    

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $purchase = Purchase::findOrFail($request['purchase_id']);
            $due = $purchase->GrandTotal - $purchase->paid_amount;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $total_paid = $purchase->paid_amount + $request['montant'];
            $new_due = $purchase->GrandTotal - $total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $purchase->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $data = $request->validated();
            $data['user_id'] = Auth::user()->id;

            $this->getModel()->create($data);

            $purchase->update([
                'paid_amount'    => $total_paid,
                'payment_status' => $payment_status,
            ]);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    public function update($id, $request)
    {
        try {
            DB::beginTransaction();

            $payment = $this->getModel()->findOrFail($id);
            $purchase = Purchase::findOrFail($payment->purchase_id);

            $old_total_paid = $purchase->paid_amount - $payment->montant;
            $due = $purchase->GrandTotal - $old_total_paid;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $new_total_paid = $old_total_paid + $request['montant'];
            $new_due = $purchase->GrandTotal - $new_total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $purchase->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->update($request->validated());

            $purchase->update([
                'paid_amount'    => $new_total_paid,
                'payment_status' => $payment_status,
            ]);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $payment = $this->getModel()->findOrFail($id);
            $purchase = Purchase::findOrFail($payment->purchase_id);

            $total_paid = $purchase->paid_amount - $payment->montant;
            $due = $purchase->GrandTotal - $total_paid;

            if ($due == 0 || $due < 0) {
                $payment_status = 'paid';
            } elseif ($due != $purchase->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->delete();

            $purchase->update([
                'paid_amount'    => $total_paid,
                'payment_status' => $payment_status,
            ]);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Destroyed Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }
}
