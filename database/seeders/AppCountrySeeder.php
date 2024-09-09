<?php

namespace Database\Seeders;

use App\Models\AppCountry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(AppCountry::TABLE_NAME)->truncate();

        DB::table(AppCountry::TABLE_NAME)->insert([
            [
                AppCountry::NAME => 'enUS',
                AppCountry::KEY => 'en_US',
                AppCountry::IMAGE => 'uk.jpg',
                AppCountry::CREATED_AT => Carbon::now()
            ],
            [
                AppCountry::NAME => 'kmKH',
                AppCountry::KEY => 'km_KH',
                AppCountry::IMAGE => 'cambodia.jpg',
                AppCountry::CREATED_AT => Carbon::now()
            ],
            [
                AppCountry::NAME => 'zhCH',
                AppCountry::KEY => 'zh_CH',
                AppCountry::IMAGE => 'china.jpg',
                AppCountry::CREATED_AT => Carbon::now()
            ],
            [
                AppCountry::NAME => 'thTH',
                AppCountry::KEY => 'th_TH',
                AppCountry::IMAGE => 'thailand.jpg',
                AppCountry::CREATED_AT => Carbon::now()
            ],
        ]);
    }
}
