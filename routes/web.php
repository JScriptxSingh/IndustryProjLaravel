<?php

Route::get('/', function (){
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::match(array('GET', 'POST'),'reporting', 'NuminixController@index');
