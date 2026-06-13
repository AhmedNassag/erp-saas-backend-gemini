<?php

namespace Modules\Inventory\Repositories\PaymentPurchaseReturn;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\PaymentPurchaseReturn\PaymentPurchaseReturn;
use Modules\Inventory\Repositories\PaymentPurchaseReturn\PaymentPurchaseReturnInterface;
use Modules\Inventory\Resources\PaymentPurchaseReturn\PaymentPurchaseReturnResource;
use Modules\Inventory\Models\PurchaseReturn\PurchaseReturn;

class PaymentPurchaseReturnRepository extends BaseRepository implements PaymentPurchaseReturnInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new PaymentPurchaseReturn();
    }

    protected function getResourceClass(): string
    {
        return PaymentPurchaseReturnResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Payment Purchase Returns';
    }

    protected function getSingularName(): string
    {
        return 'Payment Purchase Return';
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $purchaseReturn = PurchaseReturn::findOrFail($request['purchase_return_id']);
            $due = $purchaseReturn->GrandTotal - $purchaseReturn->paid_amount;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $total_paid = $purchaseReturn->paid_amount + $request['montant'];
            $new_due = $purchaseReturn->GrandTotal - $total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $purchaseReturn->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $data = $request->validated();
            $data['user_id'] = Auth::user()->id;

            $this->getModel()->create($data);

            $purchaseReturn->update([
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
            $purchaseReturn = PurchaseReturn::findOrFail($payment->purchase_return_id);

            $old_total_paid = $purchaseReturn->paid_amount - $payment->montant;
            $due = $purchaseReturn->GrandTotal - $old_total_paid;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $new_total_paid = $old_total_paid + $request['montant'];
            $new_due = $purchaseReturn->GrandTotal - $new_total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $purchaseReturn->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->update($request->validated());

            $purchaseReturn->update([
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
            $purchaseReturn = PurchaseReturn::findOrFail($payment->purchase_return_id);

            $total_paid = $purchaseReturn->paid_amount - $payment->montant;
            $due = $purchaseReturn->GrandTotal - $total_paid;

            if ($due == 0 || $due < 0) {
                $payment_status = 'paid';
            } elseif ($due != $purchaseReturn->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->delete();

            $purchaseReturn->update([
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
