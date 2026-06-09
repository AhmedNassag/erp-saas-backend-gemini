<?php

namespace Modules\Inventory\Repositories\Currency;

use App\Repositories\Base\BaseRepository;
use Modules\Inventory\Models\Currency\Currency;
use Modules\Inventory\Repositories\Currency\CurrencyInterface;
use Modules\Inventory\Resources\Currency\CurrencyResource;

class CurrencyRepository extends BaseRepository implements CurrencyInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Currency();
    }

    protected function getResourceClass(): string
    {
        return CurrencyResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Currencies';
    }

    protected function getSingularName(): string
    {
        return 'Currency';
    }

    public function store($request)
    {
        try {
            $this->getModel()->create($request->validated());

            return (new \App\Traits\API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    public function update($id, $request)
    {
        try {
            $currency = $this->getModel()->findOrFail($id);
            $currency->update($request->validated());

            return (new \App\Traits\API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    public function destroy($id)
    {
        try {
            $currency = $this->getModel()->findOrFail($id);
            $currency->delete();

            return (new \App\Traits\API)
                ->isOk(__('Destroyed Successfully'))
                ->build();
        }
        catch (\Exception $e) {
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }
}
