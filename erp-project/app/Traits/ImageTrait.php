<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

Trait ImageTrait
{
    //upload media
    public function uploadMedia($model, $collection_name, $data)
    {
        $oldMedia = $model->getMedia($collection_name)->first();
        if($oldMedia)
        {
            $file_name = $oldMedia->file_name;
            $img_id    = $oldMedia->id;
            if($img_id && $file_name)
            {
                //remove files from project
                if (File::exists(public_path('storage/'. $img_id .'/'.$file_name)))
                {
                    unlink(public_path('storage/' . $img_id .'/'.$file_name));
                }
            }
        }
        $model->addMedia($data)->toMediaCollection($collection_name); //add new record
    }
}