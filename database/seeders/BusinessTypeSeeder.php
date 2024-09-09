<?php

namespace Database\Seeders;

use App\Enums\Types\AppTypeEnum;
use App\Enums\Types\BusinessTypeHasTransaction;
use App\Models\Business;
use App\Models\BusinessType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(BusinessType::TABLE_NAME)->truncate();

        DB::table(BusinessType::TABLE_NAME)->insert([
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getProperty(),
                BusinessType::NAME => '{"local_name":"អចលនទ្រព្យ","latin_name":"Property"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'property.svg',
                BusinessType::ORDER => '1',
                BusinessType::APP_FEE => '5',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getAccommodation(),
                BusinessType::NAME => '{"local_name":"កន្លែងស្នាក់នៅ","latin_name":"Hotel"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'accommodation.svg',
                BusinessType::ORDER => '2',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getDelivery(),
                BusinessType::NAME => '{"local_name":"ដឹកឥវ៉ាន់រហ័ស","latin_name":"Delivery"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'delivery.svg',
                BusinessType::ORDER => '12',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getShop(),
                BusinessType::NAME => '{"local_name":"ហាងទំនិញលក់រាយ","latin_name":"Shop Retail"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'shop_retail.svg',
                BusinessType::ORDER => '6',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getShop(),
                BusinessType::NAME => '{"local_name":"ហាងទំនិញលក់ដុំ","latin_name":"Shop Wholesale"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'shop_wholesale.svg',
                BusinessType::ORDER => '5',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getShop(),
                BusinessType::NAME => '{"local_name":"ហាងអាហារ","latin_name":"Restaurant"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'restaurant.svg',
                BusinessType::ORDER => '7',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getAttraction(),
                BusinessType::NAME => '{"local_name":"តំបន់ទេសចរណ៍","latin_name":"Attraction"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getNo(),
                BusinessType::IMAGE => 'attraction.svg',
                BusinessType::ORDER => '3',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getNews(),
                BusinessType::NAME => '{"local_name":"សុវត្ថិភាព ភូមិឃុំ","latin_name":"Commune Safety"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getNo(),
                BusinessType::IMAGE => 'news.svg',
                BusinessType::ORDER => '10',
                BusinessType::APP_FEE => '0',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getShop(),
                BusinessType::NAME => '{"local_name":"ហាងផលិតផលក្នុងស្រុក","latin_name":"Shop Local Product"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'shop_local_product.svg',
                BusinessType::ORDER => '8',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getCharity(),
                BusinessType::NAME => '{"local_name":"រួមគ្នាជួយ","latin_name":"Together help"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getNo(),
                BusinessType::IMAGE => 'charity_and_donation.svg',
                BusinessType::ORDER => '11',
                BusinessType::APP_FEE => '0',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getMassage(),
                BusinessType::NAME => '{"local_name":"កន្លែងម៉ាស្សា","latin_name":"Massage"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'massage.svg',
                BusinessType::ORDER => '4',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getShop(),
                BusinessType::NAME => '{"local_name":"សេវាកម្ម","latin_name":"Service"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'service.svg',
                BusinessType::ORDER => '9',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getKtv(),
                BusinessType::NAME => '{"local_name":"KTV","latin_name":"KTV"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'ktv.svg',
                BusinessType::ORDER => '13',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getShop(),
                BusinessType::NAME => '{"local_name":"សហគមន៍ទំនើប","latin_name":"Modern Community"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'modern_community.svg',
                BusinessType::ORDER => '15',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getDelivery(),
                BusinessType::NAME => '{"local_name":"អ្នកដឹកជញ្ជូន","latin_name":"Carrier"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'carrier.svg',
                BusinessType::ORDER => '14',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessType::APP_TYPE_ID => AppTypeEnum::getShop(),
                BusinessType::NAME => '{"local_name":"តំណាងចែកចាយ","latin_name":"Distributor"}',
                BusinessType::HAS_TRANSACTION => BusinessTypeHasTransaction::getYes(),
                BusinessType::IMAGE => 'distributor.svg',
                BusinessType::ORDER => '16',
                BusinessType::APP_FEE => '1',
                BusinessType::STATUS => '1',
                BusinessType::CREATED_AT => Carbon::now()
            ],
        ]);
    }
}
