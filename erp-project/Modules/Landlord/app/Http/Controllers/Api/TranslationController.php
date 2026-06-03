<?php

namespace Modules\Landlord\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Landlord\Http\Requests\Translation\StoreRequest;
use Modules\Landlord\Http\Requests\Translation\UpdateRequest;
use Modules\Landlord\Models\LanguageLine;
use Modules\Landlord\Repositories\Translation\TranslationInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    protected $translationRepo;

    public function __construct(TranslationInterface $translationRepo)
    {
        $this->translationRepo = $translationRepo;
    }

    public function index(Request $request)
    {
        return $this->translationRepo->index($request);
    }

    public function store(StoreRequest $request)
    {
        return $this->translationRepo->store($request);
    }

    public function show(LanguageLine $languageLine)
    {
        return $this->translationRepo->show($languageLine);
    }

    public function update(LanguageLine $languageLine, UpdateRequest $request)
    {
        return $this->translationRepo->update($languageLine, $request);
    }

    public function destroy(LanguageLine $languageLine)
    {
        return $this->translationRepo->destroy($languageLine);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'translations'          => 'required|array',
            'translations.*.id'     => 'required|exists:landlord.language_lines,id',
        ]);

        return $this->translationRepo->bulkUpdate($request);
    }
}
