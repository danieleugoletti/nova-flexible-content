<?php

namespace Whitecube\NovaFlexibleContent\Http\Controllers;


use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\Downloadable;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaFlexibleContent\Flexible;

class FieldDownloadController extends Controller
{
    /**
     * Download the given field's contents.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(NovaRequest $request)
    {
        $resource = $request->findResourceOrFail();

        $resource->authorizeToView($request);

        preg_match('/([a-zA-z0-9]{16})__(.*)/', $request->field, $downloadMatch);
        $field = $resource->detailFields($request)
                ->filter(function ($value) {
                    return $value instanceof Flexible;
                })
                ->reduce(function ($carry, $item) use ($downloadMatch) {
                    collect($item->fieldsInLayoutByKey($downloadMatch[1]))->each(function($item) use ($carry) {
                        $carry->push($item);
                    });
                    return $carry;
                }, collect([]))
                ->filter(function ($value) use ($downloadMatch) {
                    $downloadableClass = Downloadable::class;
                    $result = $value instanceof $downloadableClass && $value->attribute==$downloadMatch[2];
                    return $result;
                });

        if (!$field->count()) {
            abort(404);
        }
        return Storage::disk($field->first()->getStorageDisk())->download($field->first()->value);
    }
}
