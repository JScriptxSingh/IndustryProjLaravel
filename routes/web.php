<?php

Route::get('/', 'NuminixController@index');
Route::post('/processData', 'NuminixController@processData');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/manage', 'HomeController@manage');
Route::get('/home/employee', 'HomeController@employee');
<<<<<<< HEAD
Route::get('home/chart','ChartController@index');
=======
Route::get('/charts', 'ChartsController@index');
>>>>>>> 846c78ac1e552c7bb93cea9cc7ddb5e0d7a75586
