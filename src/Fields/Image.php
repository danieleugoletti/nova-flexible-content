<?php

namespace Whitecube\NovaFlexibleContent\Fields;

use Laravel\Nova\Fields\Image as NovaImage;
use Illuminate\Support\Facades\Storage;
use Whitecube\NovaFlexibleContent\Http\FlexibleAttribute;

class Image extends NovaImage
{
    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $disk
     * @param  callable|null  $storageCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($name, $attribute, $disk, $storageCallback);

        $this->thumbnail(function ($value) {
            return $value ? Storage::disk($this->getStorageDisk())->url($value) : null;
        })->preview(function ($value) {
            return $value ? Storage::disk($this->getStorageDisk())->url($value) : null;
        })
        ->download(function ($request, $model) {
            $fileName = request()->input(FlexibleAttribute::REGISTER.'-download');
            return Storage::disk($this->getStorageDisk())->download($fileName);
        });
    }
}
