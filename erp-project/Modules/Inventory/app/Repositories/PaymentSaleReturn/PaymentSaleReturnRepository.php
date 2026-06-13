<?php

namespace Modules\Inventory\Repositories\PaymentSaleReturn;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\PaymentSaleReturn\PaymentSaleReturn;
use Modules\Inventory\Repositories\PaymentSaleReturn\PaymentSaleReturnInterface;
use Modules\Inventory\Resources\PaymentSaleReturn\PaymentSaleReturnResource;
use Modules\Inventory\Models\SaleReturn\SaleReturn;

class PaymentSaleReturnRepository extends BaseRepository implements PaymentSaleReturnInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new PaymentSaleReturn();
    }

    protected function getResourceClass(): string
    {
        return PaymentSaleReturnResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Payment Sale Returns';
    }

    protected function getSingularName(): string
    {
        return 'Payment Sale Return';
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $saleReturn = SaleReturn::findOrFail($request['sale_return_id']);
            $due = $saleReturn->GrandTotal - $saleReturn->paid_amount;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $total_paid = $saleReturn->paid_amount + $request['montant'];
            $new_due = $saleReturn->GrandTotal - $total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $saleReturn->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $data = $request->validated();
            $data['user_id'] = Auth::user()->id;

            $this->getModel()->create($data);

            $saleReturn->update([
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
            $saleReturn = SaleReturn::findOrFail($payment->sale_return_id);

            $old_total_paid = $saleReturn->paid_amount - $payment->montant;
            $due = $saleReturn->GrandTotal - $old_total_paid;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $new_total_paid = $old_total_paid + $request['montant'];
            $new_due = $saleReturn->GrandTotal - $new_total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $saleReturn->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->update($request->validated());

            $saleReturn->update([
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
            $saleReturn = SaleReturn::findOrFail($payment->sale_return_id);

            $total_paid = $saleReturn->paid_amount - $payment->montant;
            $due = $saleReturn->GrandTotal - $total_paid;

            if ($due == 0 || $due < 0) {
                $payment_status = 'paid';
            } elseif ($due != $saleReturn->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->delete();

            $saleReturn->update([
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
