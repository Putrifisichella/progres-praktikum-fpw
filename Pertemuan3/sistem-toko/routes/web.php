<?php

use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return 'Hello, World!';
});

Route::get('/user/{id}', function($id){
    return "User ID: ".$id;
});

Route::get('/user/{name?}', function($name='Guest'){
    return "Hello, ".$name;
});

Route::get('/profile', function(){
    return 'This is the profile page.';
});

Route::get('/redirect-to-profile',function(){
    return redirect()->route('profile');
});

Route::prefix('admin')->group(function(){
    Route::get('/dashboard', function(){
        return'Admin Dashboard';
    });

    Route::get('/profile',function(){
        return 'Admin Profile';
    });
});

Route::get('/dashboard', function(){
    return 'Welcome to your dashboard!';
})->middleware('auth');

Route::resource('posts','PostController');