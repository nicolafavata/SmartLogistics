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
        Route::get('/user-profile', 'UserController@viewProfile')->name('view-profile');
        Route::patch('update-user', 'UserController@updateProfile')->name('updateuser');
        Route::get('find-product','UserController@findProduct')->name('findproduct');

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

        //-------------------------------------------------------------------------------------------------------------------------------------------------------------
        //R E S P O N S A B I L E   A C Q U I S T I
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
        Route::get('/store-expires', 'SuppliesController@storeExpires')->name('store_expires');
        //Mapping Inventario-Fornitori
        Route::get('/mapping-providers/{id}','SuppliesController@mappingProviders')->name('mapping-providers');
        Route::get('/add-mapping/{id}', 'SuppliesController@addMapping')->name('add_mapping');
        Route::post('/delete-mapping/{id}','SuppliesController@deleteMapping')->where('id', '[0-9]+')->name('delete-mapping');
        Route::post('/del-mapping/{id}','SuppliesController@delMapping')->where('id', '[0-9]+')->name('del-mapping');
        Route::get('/store-mapping/{id}', 'SuppliesController@storeMapping')->name('store_mapping');
        //Configurazione ordini
        Route::get('/config-order/{id}','SuppliesController@configOrder')->where('id', '[0-9]+')->name('config-order');
        Route::patch('/setting-config-order','SuppliesController@settingConfig')->name('setting-config-order');

        //-------------------------------------------------------------------------------------------------------------------------------------------------------------
        //R E S P O N S A B I L E   P R O D U Z I O N E
        Route::get('/production', 'ProductionController@ViewProduction')->name('production');
        Route::get('/add-production', 'ProductionController@addProduction')->name('add_production');
        Route::post('/delete-production','ProductionController@deleteProduction')->name('delete-production');
        Route::post('/delete-production/{id}','ProductionController@delProduction')->where('id', '[0-9]+')->name('del-production');
        Route::get('/store-production', 'ProductionController@storeProduction')->name('store_production');
        Route::get('/mapping-production', 'ProductionController@ViewMappingProduction')->name('mapping-production');
        Route::post('/delete-mapping-production/{id}','ProductionController@deleteMapping')->where('id', '[0-9]+')->name('delete-mapping-production');
        Route::get('/add-mapping-production', 'ProductionController@addMapping')->name('add-mapping-production');
        Route::post('/del-mapping-production/{id}','ProductionController@delMappingProduction')->where('id', '[0-9]+')->name('del-mapping-production');
        Route::get('/store-mapping-production', 'ProductionController@storeMappingProduction')->name('store-mapping-production');

        //-------------------------------------------------------------------------------------------------------------------------------------------------------------
        //R E S P O N S A B I L E   V E N D I T E
        Route::get('/catalogue','SalesController@ViewCatalogue')->name('catalogue');
        Route::get('/add-catalogue', 'SalesController@addCatalogue')->name('add_catalogue');
        Route::patch('/setting-sales/{id}','SalesController@Setting')->where('id', '[0-9]+')->name('setting-sales');
        Route::post('/del-catalogue/{id}','SalesController@delCatalogue')->where('id', '[0-9]+')->name('del-catalogue');
        Route::patch('/delete-catalogue','SalesController@deleteCatalogue')->where('id', '[0-9]+')->name('delete-catalogue');
        Route::get('/store-catalogue', 'SalesController@storeCatalogue')->name('store-catalogue');
        Route::get('/expire-monitor','SalesController@expireMonitor')->name('expire-monitor');
        Route::patch('/setting-expire','SalesController@settingExpire')->name('setting-expire');
        //-------------------------------------------------------------------------------------------------------------------------------------------------------------
        //N U O V I  D O C U M E N T I
        Route::get('/new-sales-desk','SalesController@newSalesDesk')->name('new-sales-desk');
        Route::post('/check-number-new-sales-desk/{id}/{number}/{date}','SalesController@checkNumberNewSalesDesk')->where('id', '[0-9]+')->where('number', '[0-9]+');
        Route::post('/check-ean-new-sales/{id}/{ean}','SalesController@checkEanNewSales')->where('id', '[0-9]+')->where('number', '[0-9]+');
        Route::post('/check-codice-new-sales/{id}/{code}','SalesController@checkCodeNewSales')->where('id', '[0-9]+')->where('number', '[0-9]+');
        Route::get('/store-sales-desk','SalesController@storeSalesDesk')->name('store-sales-desk');
        Route::post('/cancel-desk-sale','SalesController@cancelDeskSale')->name('cancel-desk-sale');

        //Upload file *.csv
        Route::post('/upload-inventories','SuppliesController@uploadInventories')->name('upload-inventories');
        Route::post('/upload-expires','SuppliesController@uploadExpires')->name('upload-expires');
        Route::post('/upload-mapping/{id}','SuppliesController@uploadMapping')->where('id', '[0-9]+')->name('upload-mapping');
        Route::post('/upload-production','ProductionController@uploadProduction')->name('upload-production');
        Route::post('/upload-mapping-production','ProductionController@uploadMappingProduction')->name('upload-mapping-production');
        Route::post('/upload-catalogue','SalesController@uploadCatalogue')->name('upload-catalogue');

        //Download file *.csv
        Route::get('/download-products-import', 'SuppliesController@downloadFile')->name('download-products-import');
        Route::get('/download-historical-data', 'SuppliesController@downloadHistorical')->name('download-historical-data');
        Route::get('/download-expires-data', 'SuppliesController@downloadExpires')->name('download-expires-import');
        Route::get('/download-mapping-import', 'SuppliesController@downloadMapping')->name('download-mapping-import');
        Route::get('/download-production-import', 'ProductionController@downloadProduction')->name('download-production-import');
        Route::get('/download-mapping-production-import', 'ProductionController@downloadMappingProduction')->name('download-mapping-production-import');
        Route::get('/download-catalogue-import', 'SalesController@downloadCatalogue')->name('download-catalogue-import');

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
