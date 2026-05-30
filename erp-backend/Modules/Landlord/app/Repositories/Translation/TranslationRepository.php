<?php

namespace Modules\Landlord\Repositories\Translation;

use Modules\Landlord\Models\LanguageLine;
use Modules\Landlord\Models\Translation;
use Modules\Landlord\Repositories\BaseRepository;
use App\Traits\API;

class TranslationRepository extends BaseRepository implements TranslationInterface
{
    public function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new LanguageLine();
    }

    public function index($request)
    {
        $query = $this->getModel()->orderBy('group')->orderBy('key');

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $data = $query->get();

        return (new API)->isOk(__('Translations'))->setData($data)->build();
    }

    public function store($request)
    {
        try {
            $languageLine = LanguageLine::updateOrCreate(
                ['group' => $request->group, 'key' => $request->key],
                ['text' => $request->text]
            );

            Translation::flushCache();

            return (new API)->isOk(__('Stored Successfully'))
                ->setData($languageLine)
                ->setStatus(201)
                ->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function show($languageLine, array $with = [])
    {
        return (new API)->isOk(__('Translation Data'))->setData($languageLine)->build();
    }

    public function update($languageLine, $request)
    {
        try {
            $languageLine->update(['text' => $request->text]);
            Translation::flushCache();
            return (new API)->isOk(__('Updated Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function destroy($languageLine)
    {
        try {
            $languageLine->delete();
            Translation::flushCache();
            return (new API)->isOk(__('Destroyed Successfully'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }

    public function bulkUpdate($request)
    {
        try {
            foreach ($request->translations as $item) {
                $line = LanguageLine::find($item['id']);
                if ($line && isset($item['text'])) {
                    $line->update(['text' => array_merge($line->text, $item['text'])]);
                }
            }

            Translation::flushCache();

            return (new API)->isOk(__('Translations updated'))->build();
        } catch (\Exception $e) {
            return (new API)->isError('An Error occured')->setStatus(500)->build();
        }
    }
}
