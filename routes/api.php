<?php

use Whitecube\NovaFlexibleContent\Http\Controllers\FieldDownloadController;

Route::get('/{resource}/{resourceId}/download/{field}', [FieldDownloadController::class, 'show']);

