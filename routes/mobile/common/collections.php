<?php
/**
 * No Authentication
 */
$router->group(['prefix' => 'mobile'], function () use ($router) {
    //Get Privacy Policy
    Route::post('get_privacy_policy', 'Mobile\Common\ProfileAPIController@getPrivacyPolicy');

    //Get Support
    Route::post('get_support', 'Mobile\Common\ProfileAPIController@getSupport');
});

/**
 * Have Authentication
 */
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    //Register Business
    include('routes/register_business.php');

    //All Dropdown List
    include('routes/dropdown.php');

    //Banner
    include('routes/banner.php');

    //Video
    include('routes/video.php');

    //Profile
    include('routes/profile.php');

    //Notification
    include('routes/notification.php');

    //Shipping Address
    include('routes/shipping_address.php');

    //Bank Account
    include('routes/bank_account.php');

    //Transaction Fee
    include('routes/transaction_fee.php');

    //Favorite
    include('routes/favorite.php');

    //Share Business
    include('routes/share_business.php');

    //Dynamic Link Business
    include('routes/dynamic_link_business.php');

    //Business Rating
    include('routes/business_rating.php');

    //Business Cart
    include('routes/business_cart.php');

    //Chat Notification
    include('routes/chat_notification.php');

    //Story Business
    include('routes/story.php');
});
