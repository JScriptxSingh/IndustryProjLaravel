<?php

Route::get('/', function (){
    return view('welcome');
});
Route::post('/processData', 'NuminixController@processData');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/manage', 'NuminixController@index');