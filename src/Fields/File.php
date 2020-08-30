<?php

namespace Whitecube\NovaFlexibleContent\Fields;

use Laravel\Nova\Fields\File as NovaFile;


class File extends NovaFile
{
    public $component = 'nova-flexible-file-field';
}
