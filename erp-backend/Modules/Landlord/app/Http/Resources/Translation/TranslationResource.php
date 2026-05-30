<?php

namespace Modules\Landlord\Http\Resources\Translation;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'group' => $this->group,
            'key'   => $this->key,
            'text'  => $this->text,
        ];
    }
}
