<?php

namespace Modules\Inventory\Repositories\Client;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\File;
use Modules\Inventory\Models\Client\Client;
use Modules\Inventory\Repositories\Client\ClientInterface;
use Modules\Inventory\Resources\Client\ClientResource;

class ClientRepository extends BaseRepository implements ClientInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Client();
    }

    protected function getResourceClass(): string
    {
        return ClientResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Clients';
    }

    protected function getSingularName(): string
    {
        return 'Client';
    }

    public function store($request)
    {
        try {
            $client = $this->getModel()->create($request->validated());

            if ($request->hasFile('image')) {
                $client->clearMediaCollection('client');
                $image = $request->file('image');
                $this->uploadMedia($client, 'client', $image);
            }
            if ($request->hasFile('images')) {
                $client->clearMediaCollection('client_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($client, 'client_images', $image);
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
            $client = $this->getModel()->findOrFail($id);
            $client->update($request->validated());

            if ($request->hasFile('image')) {
                $client->clearMediaCollection('client');
                $image = $request->file('image');
                $this->uploadMedia($client, 'client', $image);
            }
            if ($request->hasFile('images')) {
                $client->clearMediaCollection('client_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($client, 'client_images', $image);
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
            $client = $this->getModel()->findOrFail($id);

            $singleMedia = $client->getMedia('client')->first();
            $multiMedia  = $client->getMedia('client_images')->all();
            if($singleMedia) {
                $client->clearMediaCollection('client');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $client->clearMediaCollection('client_images');
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

            $client->delete();

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
