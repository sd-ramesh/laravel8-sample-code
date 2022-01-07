<?php

use App\Http\Controllers\Vendor\Address;
use Illuminate\Support\Facades\Route;

/**
 * Address routes.
 */
Route::group(['prefix' => 'address'], function () {
    Route::get('/', Address\Index::class);
    Route::get('{id}', Address\Show::class);
    Route::post('store', Address\Store::class);
    Route::put('update/{id}', Address\Update::class);
    Route::delete('{id}', Address\Destroy::class);
});
