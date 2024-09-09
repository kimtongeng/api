<?php
Route::post('check_stock_product', 'Mobile\Modules\Shop\ShopOrderAPIController@checkStockProduct');
Route::post('product_order_checkout', 'Mobile\Modules\Shop\ShopOrderAPIController@productOrderCheckout');
Route::post('get_sale_list_shop', 'Mobile\Modules\Shop\ShopOrderAPIController@getSaleListShop');
Route::post('get_sale_detail_shop', 'Mobile\Modules\Shop\ShopOrderAPIController@getSaleDetailShop');
Route::post('change_status_sale_shop', 'Mobile\Modules\Shop\ShopOrderAPIController@changeStatusSaleShop');

