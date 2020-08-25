<?php

namespace Whitecube\NovaFlexibleContent\Concerns;

use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaFlexibleContent\Fields\FieldCollection;

trait HasFlexibleFieldCollection
{
    /**
     * @param NovaRequest $request
     * @return FieldCollection
     */
    public function availableFields(NovaRequest $request)
    {
        $method = $this->fieldsMethod($request);
        return FieldCollection::make(array_values($this->filter($this->{$method}($request))));
    }
}
