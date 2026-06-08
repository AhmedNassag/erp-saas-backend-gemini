<?php

namespace Modules\Inventory\Repositories\Provider;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\File;
use Modules\Inventory\Models\Provider\Provider;
use Modules\Inventory\Repositories\Provider\ProviderInterface;
use Modules\Inventory\Resources\Provider\ProviderResource;

class ProviderRepository extends BaseRepository implements ProviderInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Provider();
    }

    protected function getResourceClass(): string
    {
        return ProviderResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Providers';
    }

    protected function getSingularName(): string
    {
        return 'Provider';
    }

    public function store($request)
    {
        try {
            $provider = $this->getModel()->create($request->validated());

            if ($request->hasFile('image')) {
                $provider->clearMediaCollection('provider');
                $image = $request->file('image');
                $this->uploadMedia($provider, 'provider', $image);
            }
            if ($request->hasFile('images')) {
                $provider->clearMediaCollection('provider_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($provider, 'provider_images', $image);
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
            $provider = $this->getModel()->findOrFail($id);
            $provider->update($request->validated());

            if ($request->hasFile('image')) {
                $provider->clearMediaCollection('provider');
                $image = $request->file('image');
                $this->uploadMedia($provider, 'provider', $image);
            }
            if ($request->hasFile('images')) {
                $provider->clearMediaCollection('provider_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($provider, 'provider_images', $image);
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
            $provider = $this->getModel()->findOrFail($id);

            $singleMedia = $provider->getMedia('provider')->first();
            $multiMedia  = $provider->getMedia('provider_images')->all();
            if($singleMedia) {
                $provider->clearMediaCollection('provider');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $provider->clearMediaCollection('provider_images');
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

            $provider->delete();

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
