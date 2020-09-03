<?php

namespace Whitecube\NovaFlexibleContent\Validation;

use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator as LaravelValidator;

class Validator extends LaravelValidator
{
    /**
     * The validation rules to wrap
     *
     * @var array
     */
    public static $fileRulesToWrap = [
        'Between',
        'Dimensions',
        'File',
        'Image',
        'Max',
        'Mimes',
        'Mimetypes',
        'Min',
        'Size',
    ];

    /**
     * Validate the dimensions of an image matches the given values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function validateDimensions($attribute, $value, $parameters)
    {
        if (!($value instanceof UploadedFile) && $value) {
            return true;
        }

        return parent::validateDimensions($attribute, $value, $parameters);
    }

    /**
     * Validate the guessed extension of a file upload is in a set of file extensions.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function validateMimes($attribute, $value, $parameters)
    {
        if (!($value instanceof UploadedFile) && $value) {
            return true;
        }

        return parent::validateMimes($attribute, $value, $parameters);
    }

    /**
     * Validate the MIME type of a file upload attribute is in a set of MIME types.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @return bool
     */
    public function validateMimetypes($attribute, $value, $parameters)
    {
        if (!($value instanceof UploadedFile) && $value) {
            return true;
        }

        return parent::validateMimetypes($attribute, $value, $parameters);
    }

    /**
     * Get the value of a given attribute.
     *
     * @param  string  $attribute
     * @return mixed
     */
    protected function getValue($attribute)
    {
        $value = Arr::get($this->data, $attribute);
        $tempFileValue = Arr::get($this->data, $value);

        return $this->isValidFileInstance($tempFileValue) ? $tempFileValue : $value;
    }

}
