<?php

namespace Modules\Core\Repositories\Branch;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\File;
use Modules\Core\Models\Branch\Branch;
use Modules\Core\Repositories\Branch\BranchInterface;
use Modules\Core\Resources\Branch\BranchResource;

class BranchRepository extends BaseRepository implements BranchInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Branch();
    }

    protected function getResourceClass(): string
    {
        return BranchResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Branches';
    }

    protected function getSingularName(): string
    {
        return 'Branch';
    }

    public function store($request)
    {
        try {
            $branch = $this->getModel()->create($request->validated());

            if ($request->hasFile('image')) {
                $branch->clearMediaCollection('branch');
                $image = $request->file('image');
                $this->uploadMedia($branch, 'branch', $image);
            }
            if ($request->hasFile('images')) {
                $branch->clearMediaCollection('branch_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($branch, 'branch_images', $image);
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
            $branch = $this->getModel()->findOrFail($id);
            $branch->update($request->validated());

            if ($request->hasFile('image')) {
                $branch->clearMediaCollection('branch');
                $image = $request->file('image');
                $this->uploadMedia($branch, 'branch', $image);
            }
            if ($request->hasFile('images')) {
                $branch->clearMediaCollection('branch_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($branch, 'branch_images', $image);
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
            $branch = $this->getModel()->findOrFail($id);

            $singleMedia = $branch->getMedia('branch')->first();
            $multiMedia  = $branch->getMedia('branch_images')->all();
            if($singleMedia) {
                $branch->clearMediaCollection('branch');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $branch->clearMediaCollection('branch_images');
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

            $branch->delete();

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
