<?php

namespace Whitecube\NovaFlexibleContent\Fields;

use Whitecube\NovaFlexibleContent\Flexible;
use Whitecube\NovaFlexibleContent\Http\FlexibleAttribute;
use Laravel\Nova\Fields\FieldCollection as BaseFieldCollection;

class FieldCollection extends BaseFieldCollection
{
    /**
     * Filter the items, removing any items that don't match the given type.
     *
     * @param  string  $type
     * @return static
     */
    public function whereInstanceOf($type)
    {
        if ($downloadField = $this->checkMatchDownloadFile($type)) {
            return $downloadField;
        }

        return $this->filter(function ($value) use ($type) {
            return $value instanceof $type;
        });
    }

    /**
     * Find a given field by its attribute.
     *
     * @param string $attribute
     * @param mixed $default
     * @return \Laravel\Nova\Fields\Field|null
     */
    public function findFieldByAttribute($attribute, $default = null)
    {
        if ($field = $this->checkMatchTrixAttachment($default)) {
            return $field;
        }

        return $this->first(function ($field) use ($attribute) {
            return isset($field->attribute) &&
                $field->attribute == $attribute;
        }, $default);
    }

    /**
     * @param  string  $type
     * @return static
     */
    private function checkMatchDownloadFile($type)
    {
        preg_match('/\/.*\/\d*\/download\/(([a-zA-z0-9]{16})__(.*))/', request()->path(), $downloadMatch);
        if (request()->method() === 'GET' && count($downloadMatch)) {
            return $this->filter(function ($value) {
                return $value instanceof Flexible;
            })
            ->reduce(function ($carry, $item) use ($downloadMatch) {
                collect($item->fieldsInLayoutByKey($downloadMatch[2]))->each(function($item) use ($carry) {
                    $carry->push($item);
                });
                return $carry;
            }, $this)
            ->filter(function ($value) use ($type, $downloadMatch) {
                $result = $value instanceof $type && $value->attribute==$downloadMatch[3];
                if ($result) {
                    $value->attribute = $downloadMatch[1];
                    request()->merge([FlexibleAttribute::REGISTER.'-download' => $value->value]);
                }

                return $result;
            });
        }

        return null;
    }

    /**
     * @param mixed $default
     * @return static
     */
    private function checkMatchTrixAttachment($default)
    {
        preg_match('/\/.*\/trix-attachment\/[a-zA-z0-9]{16}__(.*)/', request()->path(), $match);
        if (in_array(request()->method(), ['POST', 'DELETE']) && count($match)) {
            return $this->filter(function ($value) {
                return $value instanceof Flexible;
            })->reduce(function ($carry, $item) {
                $item->layouts()->each(function($item) use ($carry) {
                    collect($item->fields())->each(function($item) use ($carry) {
                        $carry->push($item);
                    });
                });

                return $carry;
            }, collect([]))
            ->first(function ($value) use ($match) {
                return $value->attribute==$match[1];
            }, $default);
        }

        return null;
    }
}
