<?php

namespace Modules\Core\Country\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'name_en'=>$this->getTranslation('name' , 'en'),
            'name_ar'=>$this->getTranslation('name' , 'ar'),
            'image'=>$this->img?$this->img->localUrl:'---',
            'status'=>$this->status??null,
        ];
    }
}
