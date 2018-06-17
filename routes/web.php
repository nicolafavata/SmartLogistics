<?php

use Illuminate\Support\Facades\Session;
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
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('user/verify{token}','Auth\RegisterController@verifyUser')->name('verify');


//Protezione routing
Route::group(['middleware'=> 'auth'],
    function (){
        Route::get('/user/{cap}/{comune}', 'UserController@geostore');
        Route::get('/user', 'UserController@index')->name('user');
        Route::get('/geocode', 'UserController@geocode')->name('geocode');
        Route::get('/employee', 'EmployeeController@index')->name('employee');
        Route::get('/admin', 'BusinessController@index')->name('admin');
        Route::get('/userprofile', 'UserController@registerProfile')->name('completeuser');
        Route::get('/businessprofile', 'BusinessController@profile')->name('completebusiness');
        Route::post('/registeruserprofile', 'UserController@storeProfile')->name('registeruser');
    });
