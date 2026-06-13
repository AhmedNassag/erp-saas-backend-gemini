<?php

namespace Modules\Inventory\Repositories\PaymentSale;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\PaymentSale\PaymentSale;
use Modules\Inventory\Repositories\PaymentSale\PaymentSaleInterface;
use Modules\Inventory\Resources\PaymentSale\PaymentSaleResource;
use Modules\Inventory\Models\Sale\Sale;

class PaymentSaleRepository extends BaseRepository implements PaymentSaleInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new PaymentSale();
    }

    protected function getResourceClass(): string
    {
        return PaymentSaleResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Payment Sales';
    }

    protected function getSingularName(): string
    {
        return 'Payment Sale';
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($request['sale_id']);
            $due = $sale->GrandTotal - $sale->paid_amount;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $total_paid = $sale->paid_amount + $request['montant'];
            $new_due = $sale->GrandTotal - $total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $sale->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $data = $request->validated();
            $data['user_id'] = Auth::user()->id;

            $this->getModel()->create($data);

            $sale->update([
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
            $sale = Sale::findOrFail($payment->sale_id);

            $old_total_paid = $sale->paid_amount - $payment->montant;
            $due = $sale->GrandTotal - $old_total_paid;

            if ($request['montant'] > $due) {
                return (new \App\Traits\API)
                    ->isError(__('Payment amount exceeds due amount'))
                    ->setStatus(400)
                    ->build();
            }

            $new_total_paid = $old_total_paid + $request['montant'];
            $new_due = $sale->GrandTotal - $new_total_paid;

            if ($new_due == 0 || $new_due < 0) {
                $payment_status = 'paid';
            } elseif ($new_due != $sale->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->update($request->validated());

            $sale->update([
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
            $sale = Sale::findOrFail($payment->sale_id);

            $total_paid = $sale->paid_amount - $payment->montant;
            $due = $sale->GrandTotal - $total_paid;

            if ($due == 0 || $due < 0) {
                $payment_status = 'paid';
            } elseif ($due != $sale->GrandTotal) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            $payment->delete();

            $sale->update([
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
