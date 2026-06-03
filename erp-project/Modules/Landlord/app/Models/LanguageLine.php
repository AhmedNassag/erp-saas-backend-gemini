<?php

namespace Modules\Landlord\Models;

use Spatie\TranslationLoader\LanguageLine as SpatieLanguageLine;

class LanguageLine extends SpatieLanguageLine
{
    protected $connection = 'landlord';
}
