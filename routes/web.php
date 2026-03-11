<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Public Short URL Redirect
|--------------------------------------------------------------------------
|
| This route handles the public redirect for short codes.
| No authentication required. Must be placed AFTER other specific routes
| so it doesn't swallow application URLs.
|
*/

Route::get('/{short_code}', [RedirectController::class, 'redirect'])
    ->where('short_code', '[a-zA-Z0-9]+');
