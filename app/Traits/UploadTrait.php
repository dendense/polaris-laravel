<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    /**
     * Function for uploading one image/file
     * 
     * @return $file
     */
    public function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $file = $uploadedFile->storeAs($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }

    /**
     * Function for uploading multiple image/file
     * 
     * @return $image_url
     */
    public function multipleUpload($query)
    {
        $image_name = Str::random(20);
        $ext = Str::lower($query->getClientOriginalExtension());
        $image_full_name = $image_name . '.' . $ext;
        $upload_path = 'post/images/';
        $image_url = $upload_path . $image_full_name;
        $success = $query->move($upload_path, $image_full_name);

        return $image_url;
    }
}

?>