<?php

namespace Modules\Core\Repositories\Warehouse;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\File;
use Modules\Core\Models\Warehouse\Warehouse;
use Modules\Core\Repositories\Warehouse\WarehouseInterface;
use Modules\Core\Resources\Warehouse\WarehouseResource;

class WarehouseRepository extends BaseRepository implements WarehouseInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Warehouse();
    }

    protected function getResourceClass(): string
    {
        return WarehouseResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Warehouses';
    }

    protected function getSingularName(): string
    {
        return 'Warehouse';
    }

    public function store($request)
    {
        try {
            $warehouse = $this->getModel()->create($request->validated());

            if ($request->hasFile('image')) {
                $warehouse->clearMediaCollection('warehouse');
                $image = $request->file('image');
                $this->uploadMedia($warehouse, 'warehouse', $image);
            }
            if ($request->hasFile('images')) {
                $warehouse->clearMediaCollection('warehouse_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($warehouse, 'warehouse_images', $image);
                }
            }

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
            $warehouse = $this->getModel()->findOrFail($id);
            $warehouse->update($request->validated());

            if ($request->hasFile('image')) {
                $warehouse->clearMediaCollection('warehouse');
                $image = $request->file('image');
                $this->uploadMedia($warehouse, 'warehouse', $image);
            }
            if ($request->hasFile('images')) {
                $warehouse->clearMediaCollection('warehouse_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($warehouse, 'warehouse_images', $image);
                }
            }

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
            $warehouse = $this->getModel()->findOrFail($id);

            $singleMedia = $warehouse->getMedia('warehouse')->first();
            $multiMedia  = $warehouse->getMedia('warehouse_images')->all();
            if($singleMedia) {
                $warehouse->clearMediaCollection('warehouse');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $warehouse->clearMediaCollection('warehouse_images');
                foreach($multiMedia as $media) {
                    $file_name = $media->file_name;
                    $img_id    = $media->id;
                    if($img_id && $file_name) {
                        if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                            unlink(public_path('storage/' . $img_id .'/'.$file_name));
                        }
                    }
                }
            }

            $warehouse->delete();

            return (new \App\Traits\API)
                ->isOk(__('Destroyed Successfully'))
                ->build();
        } catch (\Exception $e) {
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }
}
