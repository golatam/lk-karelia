<?php

namespace App\Traits;

use Carbon\Carbon;
use File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ImageTrait
{
    public function createDirectory($path)
    {
        try{

            $path = public_path($path);

            if (is_file($path)) {

                $path = dirname($path);
            }

            if (!file_exists($path)) {

                File::makeDirectory($path, 0777, true);
            } else {

                File::chmod($path, 0777);
            }

            if (file_exists($path)) {

                return true;
            } else {

                return false;
            }
        } catch (\Exception $e) {

            return false;
        }
    }

    public function createImage($file, $path, $newName = false)
    {
        $pathinfo = pathinfo($file->getClientOriginalName());
        $fileName = Str::slug($pathinfo['filename']) . "." . $pathinfo['extension'];
        $file->move($path, $fileName);

        if(pathinfo($fileName, PATHINFO_EXTENSION) === 'svg') {

            return $fileName;
        }

        File::chmod("{$path}/{$fileName}", 0777);

        $this->miniatures = $this->miniatures(request()->input('entity'));

        foreach ($this->miniatures as $miniatureKey => $miniatureValue) {

            // create an image
            $image = Image::make("{$path}/{$fileName}");

            // backup status
            $image->backup();

            if($this->{$miniatureKey}['crop']) {

                $image
                    ->fit($this->{$miniatureKey}['width'], $this->{$miniatureKey}['height'], function ($constraint) {
                        $constraint->upsize();
                    })
                    ->save("{$path}/{$miniatureKey}_" . $fileName, 100)
                ;
            } else {

                $image
                    ->resize($this->{$miniatureKey}['width'], $this->{$miniatureKey}['height'], function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save("{$path}/{$miniatureKey}_" . $fileName, 100)
                ;
            }

            // reset image (return to backup state)
            $image->reset();
        }

        return $fileName;
    }

    public function removeLink($file, $dir = false)
    {
        $file = public_path($file);

        if ($dir) {

            $dir = dirname($file);
        }

        $this->miniatures = $this->miniatures(request()->input('entity'));

        foreach ($this->miniatures as $miniatureKey => $miniatureValue) {

            $filePath = dirname($file) . "/{$miniatureKey}_" . basename($file);

            if (file_exists($filePath) && is_file($filePath)){

                File::delete($filePath);
            }
        }

        if (file_exists($file) && is_file($file)){

            File::delete($file);
        }

        if ($dir) {

            File::deleteDirectory($dir);
        }
    }
}
