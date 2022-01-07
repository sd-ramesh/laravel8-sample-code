<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/ 

Route::get('/', function () {
    return Config::get('app.name').' powered by Laravel '. app()->version();
});


Route::middleware('')
    ->get('laravel', function () {
        return view('welcome');
    }); 
