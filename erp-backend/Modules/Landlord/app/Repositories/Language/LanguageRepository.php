<?php

namespace Modules\Landlord\Repositories\Language;

use Modules\Landlord\Models\Language;
use Modules\Landlord\Models\LanguageLine;
use Modules\Landlord\Models\Translation;
use Modules\Landlord\Repositories\BaseRepository;
use App\Traits\API;
use Illuminate\Support\Facades\Cache;

class LanguageRepository extends BaseRepository implements LanguageInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Language();
    }

    public function index($request)
    {
        $data = $this->getModel()->orderBy('created_at', 'desc')->get();
        return (new API)->isOk(__('Languages'))->setData($data)->build();
    }

    public function store($request)
    {
        try {
            $data = $request->validated();

            if ($data['is_default'] ?? false) {
                Language::where('is_default', true)->update(['is_default' => false]);
            }

            $language = $this->getModel()->create($data);

            Cache::forget('languages.active');
            Cache::forget('default_language');

            return (new API)->isOk(__('Stored Successfully'))
                ->setData($language)
                ->setStatus(201)
                ->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function show($language, array $with = [])
    {
        return (new API)->isOk(__('Language Data'))->setData($language)->build();
    }

    public function update($language, $request)
    {
        try {
            $data = $request->validated();

            if ($data['is_default'] ?? false) {
                Language::where('is_default', true)
                    ->where('id', '!=', $language->id)
                    ->update(['is_default' => false]);
            }

            $language->update($data);

            Cache::forget('languages.active');
            Cache::forget('default_language');

            return (new API)->isOk(__('Updated Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function destroy($language)
    {
        if ($language->is_default) {
            return (new API)->isError(__('Can not delete default language'))->setStatus(422)->build();
        }

        try {
            LanguageLine::all()->each(function ($ll) use ($language) {
                $text = $ll->text;
                unset($text[$language->code]);
                $ll->update(['text' => $text]);
            });

            $language->delete();

            Cache::forget('languages.active');
            Translation::flushCache($language->code);

            return (new API)->isOk(__('Destroyed Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }
}
