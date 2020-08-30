<?php

namespace Whitecube\NovaFlexibleContent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Whitecube\NovaFlexibleContent\Http\FlexibleAttribute;
use Whitecube\NovaFlexibleContent\Http\ParsesFlexibleAttributes;
use Whitecube\NovaFlexibleContent\Http\TransformsFlexibleErrors;
use Whitecube\NovaFlexibleContent\Http\FlexibleDeleteNotUsedFiles;

class InterceptFlexibleAttributes
{
    use ParsesFlexibleAttributes;
    use TransformsFlexibleErrors;

    /**
     * Handle the given request and get the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next) : Response
    {
        if (!$this->requestHasParsableFlexibleInputs($request)) {
            return $next($request);
        }

        $flexibleDeleteNotUsedFiles = FlexibleDeleteNotUsedFiles::make($request->input(FlexibleDeleteNotUsedFiles::FILES_TO_DELETE));
        $request->request->remove(FlexibleDeleteNotUsedFiles::FILES_TO_DELETE);

        $request->merge($this->getParsedFlexibleInputs($request));
        $request->request->remove(FlexibleAttribute::REGISTER);

        $response = $next($request);

        if ($this->shouldTransformFlexibleErrors($response)) {
            return $this->transformFlexibleErrors($response);
        }

        $flexibleDeleteNotUsedFiles->deleteFiles();

        return $response;
    }
}
