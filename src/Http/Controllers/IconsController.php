<?php

namespace MityDigital\Iconamic\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MityDigital\Iconamic\Facades\Iconamic;

class IconsController
{
    /**
     * Return the icon list for a field's folder settings, as passed in the fieldtype's signed preload URL
     */
    public function __invoke(Request $request): JsonResponse
    {
        // The signature proves the fieldtype generated these settings, so a user cannot point it at another folder.
        if (! $request->hasValidRelativeSignature()) {
            abort(404);
        }

        $icons = Iconamic::icons(
            (string) $request->query('path'),
            (string) $request->query('path_helper', 'default'),
            (string) $request->query('recursive', 'default'),
        );

        // The list only changes when icons are added to the folder, so let the browser reuse it between pages.
        return response()
            ->json(['icons' => $icons])
            ->setPrivate()
            ->setMaxAge(3600);
    }
}
