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

//PROVA PDF -----------------------------------------------------------------------------------
Route::get('pdfview',array('as'=>'pdfview','uses'=>'Itemcontroller@pdfview'));
//PROVA PDF -----------------------------------------------------------------------------------

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

        //Impiegati account e funzioni responsabile della sede
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
        Route::get('/visiblecompany','EmployeeController@visibleCompany')->name('visiblecompany');
        Route::post('/changemyvisible', 'EmployeeController@changeVisible')->name('changemyvisible');
        Route::get('/supplyresearch','EmployeeController@researchCompany')->name('supplyresearch');
        Route::post('/researchsupply', 'EmployeeController@findCompany')->name('supplyfind');
        Route::post('/request-supply/{id}','EmployeeController@requestSupply')->where('id', '[0-9]+')->name('request-supply');
        Route::get('/requeststransmitted','EmployeeController@requestsTransmitted')->name('requests-transmitted');
        Route::post('/cancel-request/{id}','EmployeeController@cancelRequest')->where('id', '[0-9]+')->name('cancel-request');
        Route::post('/retransmit-request/{id}','EmployeeController@retransmitRequest')->where('id', '[0-9]+')->name('retransmit-request');
        Route::get('/requestsreceived','EmployeeController@requestsReceived')->name('requests-received');
        Route::post('/block-company/{id}','EmployeeController@blockRequest')->where('id', '[0-9]+')->name('block-company');
        Route::post('/cancel-company-request/{id}','EmployeeController@cancelCompanyRequest')->where('id', '[0-9]+')->name('cancel-company-request');
        Route::post('/accept-request/{id}','EmployeeController@AcceptRequest')->where('id', '[0-9]+')->name('accept-request');
        Route::get('supply-chain-management','EmployeeController@supplyChainManagement')->name('supplychainmanagement');
        Route::post('delete-supply-chain-management/{id}','EmployeeController@deleteSupplyChain')->where('id', '[0-9]+')->name('delete-supply');
        Route::get('manager-supply-chain/{id}','EmployeeController@managerSupplyChain')->where('id', '[0-9]+')->name('manage-supply');
        Route::patch('updatesupplychain','EmployeeController@updateSupplyChain')->name('updatesupplychain');
        Route::get('/block-supply-chain','EmployeeController@ViewBlockSupply')->name('block-supply');
        Route::post('/sblock-company/{id}','EmployeeController@sblockRequest')->where('id', '[0-9]+')->name('sblock-company');

        //Responsabile acquisti
        Route::get('/providers', 'SuppliesController@ViewProvider')->name('providers');
        Route::post('/delete-provider/{id}','SuppliesController@deleteProvider')->where('id', '[0-9]+')->name('delete-provider');
        Route::get('/create-provider','SuppliesController@addProvider')->name('add_provider');
        Route::post('/addnewprovider','SuppliesController@addNewProvider')->name('addnewprovider');
        Route::get('/updateprovider/{id}','SuppliesController@upProvider')->name('update-provider');
        Route::patch('/update-provider','SuppliesController@updateProvider')->name('update-provider-db');
        //Inventario
        Route::get('/inventories', 'SuppliesController@ViewInventories')->name('inventories');
        Route::post('/delete-inventories','SuppliesController@deleteInventories')->name('delete-inventories');
        Route::get('/add-inventories', 'SuppliesController@addInventories')->name('add_item');
        Route::get('/store-inventories', 'SuppliesController@storeInventories')->name('store_item');
        //Scadenze
        Route::get('/expires', 'SuppliesController@ViewExpires')->name('expires');
        Route::post('/delete-expires','SuppliesController@deleteExpires')->name('delete-expires');
        Route::get('/add-expires', 'SuppliesController@addExpires')->name('add_expires');
        Route::get('/update-expires/{id}','SuppliesController@upExpires')->where('id', '[0-9]+')->name('update-expires');
        Route::post('/delete-expires/{id}','SuppliesController@delExpires')->where('id', '[0-9]+')->name('del-expires');


        //Upload file *.csv
        Route::post('/upload-inventories','SuppliesController@uploadInventories')->name('upload-inventories');

        //Download file *.csv
        Route::get('/download-products-import', 'SuppliesController@downloadFile')->name('download-products-import');
        Route::get('/download-historical-data', 'SuppliesController@downloadHistorical')->name('download-historical-data');

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
        Route::get('/admin/our_companies', 'BusinessController@viewCompany')->name('viewcompany');
        Route::post('/admin/delete/companies/{id}', 'BusinessController@deleteCompany')->where('id', '[0-9]+')->name('deletecompany');

        //Registrazione
        Route::get('/userprofile', 'UserController@registerProfile')->name('completeuser');
        Route::get('/businessprofile', 'BusinessController@registerProfile')->name('completebusiness');
        Route::post('/registeruserprofile', 'UserController@CreateUserProfile')->name('registeruser');
        Route::post('/registerbusinessprofile', 'BusinessController@CreateBusinessProfile')->name('registerbusiness');
    });

//Route script in batch => http://www.smartlogis.it/batch/verifyoiGgYJpzqVtljQSwUry9BPITcyEzmbzVBFUgjc2KIPEFptPwNccS8jLhgfT7 ATTENZIONE!!!! dopo verify scrivere il token
Route::get('/batch/verify{token}','BatchController@verifyToken');
