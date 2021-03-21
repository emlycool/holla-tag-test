<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\LazyCollection;

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
    // return view('welcome');
    Collection::times($number = 1000000, function(){
        return [
            'name' => 'world world world world world world world world',
            'phone' => 'world world world world world world world world',
            'age' => 'world world world world world world world world',
            'sex' => 'world world world world world world world world',
            'gender' => 'world world world world world world world world',
            'house' => 'world world world world world world world world',
            'card' => 'world world world world world world world world',
            'book' => 'world world world world world world world world',
        ];
    });
    // $users = User::all();
});
