<?php

namespace Modules\Landlord\Models;

use Illuminate\Support\Facades\Cache;

class Translation
{
    public static function get(string $group, string $key, string $locale, ?string $default = null): ?string
    {
        $line = LanguageLine::where('group', $group)->where('key', $key)->first();

        if ($line && isset($line->text[$locale])) {
            return $line->text[$locale];
        }

        // Fallback: default language
        $defaultLang = Cache::rememberForever('default_language', function () {
            return Language::getDefault();
        });

        if ($defaultLang && $locale !== $defaultLang->code && $line && isset($line->text[$defaultLang->code])) {
            return $line->text[$defaultLang->code];
        }

        return $default ?? "$group.$key";
    }

    public static function getAllForLanguage(string $locale): array
    {
        return Cache::rememberForever("translations.$locale", function () use ($locale) {
            return LanguageLine::all()
                ->mapWithKeys(fn($ll) => ["{$ll->group}.{$ll->key}" => $ll->text[$locale] ?? null])
                ->filter()
                ->all();
        });
    }

    public static function flushCache(?string $locale = null): void
    {
        if ($locale) {
            Cache::forget("translations.$locale");
        } else {
            Language::pluck('code')->each(fn($code) => Cache::forget("translations.$code"));
        }
        Cache::forget('default_language');
    }
}
