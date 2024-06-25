<?php

namespace App\Http\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadAble
{
    /**
     * @param  string  $disk
     * @return false|string
     */
    public function uploadOne(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = ! is_null($filename) ? $filename : uniqid('FILE_').dechex(time());

        return $file->storeAs(
            $folder,
            $name.'.'.$file->getClientOriginalExtension(),
            $disk
        );
    }

    /**
     * @param  string  $disk
     */
    public function deleteOne($path = null, $disk = 'public')
    {
        Storage::disk($disk)->delete($path);
    }
}
