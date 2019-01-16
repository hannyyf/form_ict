<?php

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
    return view('auth.login');
});

Auth::routes();
Route::get('/home', 'HomeController@reIndex')->name('home');
Route::get('/index', 'HomeController@reIndex');

//djmapplogin
Route::post('djmapplogin', 'ApiController@GetLogin');

// Request FPPB
Route::resource('requestfppb','RequestFPPBController'); //tambahkan baris ini
Route::get('editfppb/{notrx}','RequestFPPBController@edit');
Route::post('/update','RequestFPPBController@storeEdit');
Route::get('/fetch', 'RequestFPPBController@fetch')->name('requestfppb.fetch');

//Approve Div Head
Route::resource('approvedivhead','ApproveFPPBDivHeadController');
Route::get('detaildh/{notrx}','ApproveFPPBDivHeadController@edit');

//Approve Director
Route::resource('approvedirector','ApproveFPPBDirector');
Route::get('detaildir/{notrx}','ApproveFPPBDirector@edit');

// Review ICT
Route::resource('reviewict','ReviewICTController');
Route::get('detailict/{notrx}','ReviewICTController@edit');

// Review ICT Manager
Route::resource('approveictmgr','ApproveICTManager');
Route::get('detailappict/{notrx}','ApproveICTManager@edit');

// Approve DIC
Route::resource('approvedic','ApproveDICController');
Route::get('detaildic/{notrx}','ApproveDICController@edit');

// Mapping ICT
Route::resource('mappingict','MappingController');
Route::get('detailmapping/{notrx}','MappingController@edit');

// Serah Terima 
Route::resource('serahterima','RequestCloseController');
Route::get('detailrequestclose/{notrx}','RequestCloseController@edit');
Route::get('/detailrequested','RequestCloseController@indexRequested')->name('serahterima.reindex');
Route::get('detailrequestedclose/{notrx}','RequestCloseController@editRequested');
Route::post('/closedrequest','RequestCloseController@storeEdit');

//report monitoring
Route::resource('report','ReportMonitoringController');
Route::get('detailreport/{notrx}','ReportMonitoringController@edit');
Route::get('/filter', 'ReportMonitoringController@filter')->name('report.filter');
Route::get('/listtransfer', 'ReportMonitoringController@indexTransfer')->name('transfer.index');
Route::get('/find', 'ReportMonitoringController@findTransfer')->name('transfer.find');
Route::get('detailtransfer/{notrx}','ReportMonitoringController@editTransfer');
Route::get('/kategori', 'ReportMonitoringController@getKategori')->name('transfer.loadkategori');
Route::post('/updatekategori','ReportMonitoringController@updateKategori')->name('transfer.updatekategori');
Route::get('/getdetail', 'ReportMonitoringController@getdetail')->name('report.detail');
Route::get('/getdetailpr', 'ReportMonitoringController@getdetailpr')->name('report.detailpr');