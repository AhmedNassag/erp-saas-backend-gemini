<?php

namespace Modules\Inventory\Repositories\Category;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\File;
use Modules\Inventory\Models\Category\Category;
use Modules\Inventory\Repositories\Category\CategoryInterface;
use Modules\Inventory\Resources\Category\CategoryResource;

class CategoryRepository extends BaseRepository implements CategoryInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Category();
    }

    protected function getResourceClass(): string
    {
        return CategoryResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Categories';
    }

    protected function getSingularName(): string
    {
        return 'Category';
    }

    public function store($request)
    {
        try {
            $data = $request->validated();
            if (($data['is_main'] ?? false) || $request->boolean('is_main')) {
                $data['category_id'] = null;
            }
            $category = $this->getModel()->create($data);

            if ($request->hasFile('image')) {
                $category->clearMediaCollection('category');
                $image = $request->file('image');
                $this->uploadMedia($category, 'category', $image);
            }
            if ($request->hasFile('images')) {
                $category->clearMediaCollection('category_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($category, 'category_images', $image);
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
            $category = $this->getModel()->findOrFail($id);
            $data = $request->validated();
            if (($data['is_main'] ?? false) || $request->boolean('is_main')) {
                $data['category_id'] = null;
            }
            $category->update($data);

            if ($request->hasFile('image')) {
                $category->clearMediaCollection('category');
                $image = $request->file('image');
                $this->uploadMedia($category, 'category', $image);
            }
            if ($request->hasFile('images')) {
                $category->clearMediaCollection('category_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($category, 'category_images', $image);
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
            $category = $this->getModel()->findOrFail($id);

            $singleMedia = $category->getMedia('category')->first();
            $multiMedia  = $category->getMedia('category_images')->all();
            if($singleMedia) {
                $category->clearMediaCollection('category');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $category->clearMediaCollection('category_images');
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

            $category->delete();

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
