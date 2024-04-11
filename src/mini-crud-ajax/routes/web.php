<?php
    use App\Http\Controllers\FrontController;
    use App\Http\Controllers\AccesorioController;

    Route::get('/', 'FrontController@index')->name('front.index');

    Route::get('/accesorios', 'AccesorioController@index')->name('accesorios.index');
    Route::post('/postAccesorio', 'AccesorioController@store')->name('accesorios.store');
    Route::delete('/deleteAccesorio/{accesorio}', 'AccesorioController@destroy')->name('accesorios.delete');


