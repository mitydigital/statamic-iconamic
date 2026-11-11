<?php

use Illuminate\Support\Facades\Route;
use MityDigital\Iconamic\Http\Controllers\IconsController;

Route::get('iconamic/icons', IconsController::class)->name('iconamic.icons');
