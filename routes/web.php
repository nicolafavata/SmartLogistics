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

        //Impiegati account
        Route::get('/newpassword', 'EmployeeController@newPassword')->name('new_password');
        Route::get('/profile', 'EmployeeController@myProfile')->name('my_profile');
        Route::get('/mypicture', 'EmployeeController@picture')->name('picture');
        Route::patch('/updatemypicture','EmployeeController@updatePicture')->name('changemypicture');
        Route::get('/updateprofile','EmployeeController@upProfile')->name('upprofile');
        Route::patch('/updatemyprofile','EmployeeController@updateMyProfile')->name('changemyprofile');
        Route::get('/company','EmployeeController@myCompany')->name('my_company');
        Route::get('/updatecompany','EmployeeController@upCompany')->name('upcompany');
        Route::patch('/updatemycompany','EmployeeController@updateMyCompany')->name('changemycompany');
        Route::get('/addemployee','EmployeeController@addEmployee')->name('addemployee');
        Route::post('/addnewemployee','EmployeeController@addNewEmployee')->name('addnewemployee');
        Route::get('/viewemployees','EmployeeController@viewEmployees')->name('viewemployees');
        Route::get('/updateemployees','EmployeeController@upEmployees')->name('upemployee');
        Route::patch('updateemployee','EmployeeController@updateEmployee')->name('update-employee');
        Route::get('/deleteemployees','EmployeeController@delEmployees')->name('delemployee');
        Route::post('/deleteemployees/{id}','EmployeeController@delEmployee')->where('id', '[0-9]+')->name('delete-employee');



        //Amministratori
        Route::get('/admin', 'BusinessController@index')->name('admin');
        Route::get('/admin/profile', 'BusinessController@viewProfile')->name('adminprofile');
        Route::patch('/admin/updateprofile', 'BusinessController@updateProfile')->name('updateprofile');
        Route::get('/admin/company', 'BusinessController@addCompany')->name('addcompany');
        Route::post('/admin/addcompany', 'BusinessController@addNewCompany')->name('addnewcompany');
        Route::get('/admin/logo', 'BusinessController@logoView')->name('logobusiness');
        Route::patch('/admin/updatelogo', 'BusinessController@updateLogo')->name('updatelogo');
        Route::get('/admin/descrizione', 'BusinessController@viewDesc')->name('desc_business');
        Route::patch('/admin/updatedesc', 'BusinessController@updateDesc')->name('updatedesc');
        Route::get('/admin/contatti', 'BusinessController@viewContatti')->name('contattibusiness');
        Route::patch('/admin/updatecontatti', 'BusinessController@updateContatti')->name('updatecontatti');
        Route::get('admin/our_companies', 'BusinessController@viewCompany')->name('viewcompany');
        Route::post('admin/delete/companies/{id}', 'BusinessController@deleteCompany')->where('id', '[0-9]+')->name('deletecompany');

        //Registrazione
        Route::get('/userprofile', 'UserController@registerProfile')->name('completeuser');
        Route::get('/businessprofile', 'BusinessController@registerProfile')->name('completebusiness');
        Route::post('/registeruserprofile', 'UserController@CreateUserProfile')->name('registeruser');
        Route::post('/registerbusinessprofile', 'BusinessController@CreateBusinessProfile')->name('registerbusiness');
    });
