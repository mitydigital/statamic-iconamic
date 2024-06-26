<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Path
    |--------------------------------------------------------------------------
    |
    | This is your default path to your collection of icons.
    |
    | You can override this per field usage.
    |
    */

    'path' => 'icons',

    /*
    |--------------------------------------------------------------------------
    | Path Helper
    |--------------------------------------------------------------------------
    |
    | This sets up the Laravel path helper to use when resolving and icon
    | location.
    |
    | You can use any of these path helpers:
    |   app_path
    |   base_path
    |   public_path
    |   resource_path
    |   storage_path
    |
    | You can override this per field usage.
    |
    */

    'path_helper' => 'resource_path',

    /*
    |--------------------------------------------------------------------------
    | Recursively list icons?
    |--------------------------------------------------------------------------
    |
    | When enabled, Iconamic will recursively list icons in your configured
    | paths. This is really handy for helping organise sites with many icons.
    |
    | When disabled, only icons in the exact path configuration are listed.
    |
    */

    'recursive' => false,

];
