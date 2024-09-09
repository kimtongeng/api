<?php 
Route::post("/admin/typeItemFake/get","TypeItemFakeController@index");
Route::post("/admin/typeItemFake","TypeItemFakeController@store");
Route::post("/admin/typeItemFake/delete","TypeItemFakeController@destroy");
Route::post("/admin/typeItemFake/update","TypeItemFakeController@update");