<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api', 'namespace' => 'Api'], function()
{
	Route::post('/signup', 'AuthController@signup')->name('signup');
	Route::post('/signin', 'AuthController@signin')->name('signin');
	Route::post('/get-auth-user', 'AuthController@getAuthUser')->name('getAuthUser');


	Route::get('/discuss', 'DiscussionController@index')->name('discussAll');
	Route::post('/discuss', 'DiscussionController@store')->name('discuss.store');
	Route::post('/discuss/comment', 'DiscussionController@storeComment')->name('comment.store');
	Route::get('/discuss/channels', 'DiscussionController@channelList')->name('channelList');
	Route::get('/discuss/channel/{slug}', 'DiscussionController@discussChannel')->name('discussChannel');
	Route::get('/discuss/{slug}', 'DiscussionController@show')->name('discussSingle');
	
});
