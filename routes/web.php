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
        //Cittadini
        Route::get('/user/{cap}/{comune}', 'UserController@geostore');
        Route::get('/user', 'UserController@index')->name('user');
        Route::get('/geocode', 'UserController@geocode')->name('geocode');

        //Impiegati
        Route::get('/employee', 'EmployeeController@index')->name('employee');

        //Amministratori
        Route::get('/admin', 'BusinessController@index')->name('admin');
        Route::get('/admin/profile', 'BusinessController@viewProfile')->name('adminprofile');
        Route::patch('/admin/updateprofile', 'BusinessController@updateProfile')->name('updateprofile');
        Route::post('/admin/company', 'BusinessController@addCompany')->name('addcompany');
        Route::post('/admin/addcompany', 'BusinessController@addNewCompany')->name('addnewcompany');

        Route::get('/admin/logo', 'BusinessController@logoView')->name('logobusiness');
        Route::patch('/admin/updatelogo', 'BusinessController@updateLogo')->name('updatelogo');
        Route::get('/admin/descrizione', 'BusinessController@viewDesc')->name('desc_business');
        Route::patch('/admin/updatedesc', 'BusinessController@updateDesc')->name('updatedesc');
        Route::get('/admin/contatti', 'BusinessController@viewContatti')->name('contattibusiness');
        Route::patch('/admin/updatecontatti', 'BusinessController@updateContatti')->name('updatecontatti');
        Route::get('admin/our_companies', 'BusinessController@viewCompany')->name('viewcompany');

        //Registrazione
        Route::get('/userprofile', 'UserController@registerProfile')->name('completeuser');
        Route::get('/businessprofile', 'BusinessController@registerProfile')->name('completebusiness');
        Route::post('/registeruserprofile', 'UserController@CreateUserProfile')->name('registeruser');
        Route::post('/registerbusinessprofile', 'BusinessController@CreateBusinessProfile')->name('registerbusiness');
    });
