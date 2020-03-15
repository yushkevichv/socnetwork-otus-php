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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/users', 'UserController@index')->name('user.index');
    Route::get('/users/{id}', 'UserController@show')->name('user.show');

   Route::get('/chats', 'ChatController@store')->name('chat.store');
   Route::get('/chat/{id}', 'MessageController@index')->name('messages.user_index');
   Route::post('/chat/{id}', 'MessageController@store')->name('messages.store');

   Route::post('subscribe/{id}', 'SubscribeController@subscribe')->name('feed.subscribe');
   Route::post('unsubscribe/{id}', 'SubscribeController@unsubscribe')->name('feed.unsubscribe');
});