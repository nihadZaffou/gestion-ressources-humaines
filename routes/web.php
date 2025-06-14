<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{any}', function () {
    $path = public_path('assets/index.html'); // أو public_path('index.html') حسب البنية ديالك

    if (File::exists($path)) {
        return Response::make(File::get($path), 200)
            ->header("Content-Type", "text/html");
    } else {
        abort(404);
    }
})->where('any', '^(?!assets|js|css|images|fonts|favicon\.ico).*$');
    