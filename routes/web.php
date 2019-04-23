<?php

Route::get('/', 'NuminixController@index');
Route::post('/processData', 'NuminixController@processData');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/manage', 'HomeController@manage');
Route::get('/home/employee', 'HomeController@employee');