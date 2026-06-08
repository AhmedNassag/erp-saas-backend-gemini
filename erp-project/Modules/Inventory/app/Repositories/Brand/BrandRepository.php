<?php

namespace Modules\Inventory\Repositories\Brand;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\File;
use Modules\Inventory\Models\Brand\Brand;
use Modules\Inventory\Repositories\Brand\BrandInterface;
use Modules\Inventory\Resources\Brand\BrandResource;

class BrandRepository extends BaseRepository implements BrandInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Brand();
    }

    protected function getResourceClass(): string
    {
        return BrandResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Brands';
    }

    protected function getSingularName(): string
    {
        return 'Brand';
    }

    public function store($request)
    {
        try {
            $brand = $this->getModel()->create($request->validated());

            if ($request->hasFile('image')) {
                $brand->clearMediaCollection('brand');
                $image = $request->file('image');
                $this->uploadMedia($brand, 'brand', $image);
            }
            if ($request->hasFile('images')) {
                $brand->clearMediaCollection('brand_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($brand, 'brand_images', $image);
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
            $brand = $this->getModel()->findOrFail($id);
            $brand->update($request->validated());

            if ($request->hasFile('image')) {
                $brand->clearMediaCollection('brand');
                $image = $request->file('image');
                $this->uploadMedia($brand, 'brand', $image);
            }
            if ($request->hasFile('images')) {
                $brand->clearMediaCollection('brand_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($brand, 'brand_images', $image);
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
            $brand = $this->getModel()->findOrFail($id);

            $singleMedia = $brand->getMedia('brand')->first();
            $multiMedia  = $brand->getMedia('brand_images')->all();
            if($singleMedia) {
                $brand->clearMediaCollection('brand');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $brand->clearMediaCollection('brand_images');
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

            $brand->delete();

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
