<?php
Route::post('add_favorite', 'Mobile\Common\FavoriteAPIController@addFavorite');
Route::post('remove_favorite', 'Mobile\Common\FavoriteAPIController@removeFavorite');
Route::post('get_favorite', 'Mobile\Common\FavoriteAPIController@getFavorite');
