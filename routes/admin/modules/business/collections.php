<?php
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    /** Business Type Route */
    include('routes/business_type.php');

    /**Update App Fee */
    include('routes/app_fee.php');

    /** Property Route */
    include('routes/property_list.php');

    /** Attraction Route */
    include('routes/attraction_list.php');

    /** Shop Route */
    include('routes/shop/shop_category.php');
    include('routes/shop/shop_list.php');

    /** Hotel Route (Accommodation) */
    include('routes/accommodation/attribute.php');
    include('routes/accommodation/accommodation_category.php');
    include('routes/accommodation/accommodation_list.php');

    /** Charity Route */
    include('routes/charity/charity_list.php');
    include('routes/charity/charity_category.php');

    /** Massage Route */
    include('routes/massage/massage_list.php');
    include('routes/massage/massage_therapist_list.php');

    /** Society Security Route */
    include('routes/society_security/event_type.php');
    include('routes/society_security/security_code.php');
    include('routes/society_security/position_group.php');
    include('routes/society_security/news_recipient.php');

    /**KTV Route */
    include('routes/ktv/ktv_list.php');

    /** Delivery Route */
    include('routes/delivery/vehicle_type.php');
    include('routes/delivery/item_type.php');
});
