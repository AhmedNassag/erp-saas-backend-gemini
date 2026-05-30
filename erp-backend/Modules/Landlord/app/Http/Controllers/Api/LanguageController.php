<?php

namespace Modules\Landlord\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Landlord\Http\Requests\Language\StoreRequest;
use Modules\Landlord\Http\Requests\Language\UpdateRequest;
use Modules\Landlord\Models\Language;
use Modules\Landlord\Models\LanguageLine;
use Modules\Landlord\Models\Translation;
use Modules\Landlord\Repositories\Language\LanguageInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    protected $languageRepo;

    public function __construct(LanguageInterface $languageRepo)
    {
        $this->languageRepo = $languageRepo;
    }

    // ─── Public ────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        $languages = Cache::rememberForever('languages.active', function () {
            return Language::getActive()->makeHidden('is_active');
        });

        return response()->json(['status' => 'success', 'data' => $languages]);
    }

    public function show(string $code): JsonResponse
    {
        $language = Language::where('code', $code)->first();

        if (!$language || !$language->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Language not found.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $language]);
    }

    public function translations(string $code): JsonResponse
    {
        $language = Language::where('code', $code)->where('is_active', true)->first();

        if (!$language) {
            return response()->json(['status' => 'error', 'message' => 'Language not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => Translation::getAllForLanguage($code),
        ]);
    }

    public function translationsByGroup(string $code, string $group): JsonResponse
    {
        $language = Language::where('code', $code)->where('is_active', true)->first();

        if (!$language) {
            return response()->json(['status' => 'error', 'message' => 'Language not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => LanguageLine::where('language_code', $code)
                ->where('group', $group)
                ->pluck('value', 'key'),
        ]);
    }

    // ─── Super Admin ───────────────────────────────────────────

    public function adminIndex(Request $request)
    {
        return $this->languageRepo->index($request);
    }

    public function store(StoreRequest $request)
    {
        return $this->languageRepo->store($request);
    }

    public function update(Language $language, UpdateRequest $request)
    {
        return $this->languageRepo->update($language, $request);
    }

    public function destroy(Language $language)
    {
        return $this->languageRepo->destroy($language);
    }
}
