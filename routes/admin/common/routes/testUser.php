<?php

Route::post("/testUser/register","TestUserController@register");
Route::Post("/testUser/login","TestUserController@login");
Route::post("/testUser/getUser","TestUserController@getUser");
Route::post("/testUser/logout","TestUserController@logout");