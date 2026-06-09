<?php

namespace Modules\Inventory\Repositories\Unit;

use App\Repositories\Base\BaseRepository;
use Modules\Inventory\Models\Unit\Unit;
use Modules\Inventory\Repositories\Unit\UnitInterface;
use Modules\Inventory\Resources\Unit\UnitResource;

class UnitRepository extends BaseRepository implements UnitInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Unit();
    }

    protected function getResourceClass(): string
    {
        return UnitResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Units';
    }

    protected function getSingularName(): string
    {
        return 'Unit';
    }

    public function store($request)
    {
        try {
            $data = $request->validated();

            if (empty($data['base_unit'])) {
                $data['operator'] = '*';
                $data['operator_value'] = 1;
                $data['base_unit'] = null;
            }

            $this->getModel()->create($data);

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
            $unit = $this->getModel()->findOrFail($id);
            $data = $request->validated();

            if (empty($data['base_unit']) || $data['base_unit'] == $id) {
                $data['operator'] = '*';
                $data['operator_value'] = 1;
                $data['base_unit'] = null;
            }

            $unit->update($data);

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
            $unit = $this->getModel()->findOrFail($id);
            $unit->delete();

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
