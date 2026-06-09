<?php

namespace Modules\Inventory\Repositories\Setting;

use App\Repositories\Base\BaseRepository;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Modules\Inventory\Models\Setting\Setting;
use Modules\Inventory\Repositories\Setting\SettingInterface;
use Modules\Inventory\Resources\Setting\SettingResource;

class SettingRepository extends BaseRepository implements SettingInterface
{
    use ImageTrait;

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Setting();
    }

    protected function getResourceClass(): string
    {
        return SettingResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Settings';
    }

    protected function getSingularName(): string
    {
        return 'Setting';
    }

    public function index($request, $filter = null): JsonResponse
    {
        $setting = $this->getModel()->first();

        if ($setting) {
            return (new \App\Traits\API)
                ->isOk(__('Setting'))
                ->setData(new SettingResource($setting))
                ->build();
        }

        return (new \App\Traits\API)
            ->isOk(__('Setting'))
            ->setData(null)
            ->build();
    }

    public function store($request)
    {
        try {
            $existing = $this->getModel()->first();
            if ($existing) {
                return $this->update($existing->id, $request);
            }

            $setting = $this->getModel()->create($request->validated());

            if ($request->hasFile('image')) {
                $setting->clearMediaCollection('setting');
                $image = $request->file('image');
                $this->uploadMedia($setting, 'setting', $image);
            }
            if ($request->hasFile('images')) {
                $setting->clearMediaCollection('setting_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($setting, 'setting_images', $image);
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
            $setting = $this->getModel()->findOrFail($id);
            $setting->update($request->validated());

            if ($request->hasFile('image')) {
                $setting->clearMediaCollection('setting');
                $image = $request->file('image');
                $this->uploadMedia($setting, 'setting', $image);
            }
            if ($request->hasFile('images')) {
                $setting->clearMediaCollection('setting_images');
                foreach($request->file('images') as $image) {
                    $this->uploadMedia($setting, 'setting_images', $image);
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
            $setting = $this->getModel()->findOrFail($id);

            $singleMedia = $setting->getMedia('setting')->first();
            $multiMedia  = $setting->getMedia('setting_images')->all();
            if($singleMedia) {
                $setting->clearMediaCollection('setting');
                $file_name = $singleMedia->file_name;
                $img_id    = $singleMedia->id;
                if($img_id && $file_name) {
                    if (File::exists(public_path('storage/'. $img_id .'/'.$file_name))) {
                        unlink(public_path('storage/' . $img_id .'/'.$file_name));
                    }
                }
            }
            if($multiMedia) {
                $setting->clearMediaCollection('setting_images');
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

            $setting->delete();

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
