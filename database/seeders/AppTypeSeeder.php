<?php

namespace Database\Seeders;

use App\Models\AppType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(AppType::TABLE_NAME)->truncate();

        DB::table(AppType::TABLE_NAME)->insert([
            [
                AppType::NAME => 'Property',
                AppType::CREATED_AT => Carbon::now()
            ],
            [
                AppType::NAME => 'Shop',
                AppType::CREATED_AT => Carbon::now()
            ],
            [
                AppType::NAME => 'Accommodation',
                AppType::CREATED_AT => Carbon::now()
            ],
            [
                AppType::NAME => 'Attraction',
                AppType::CREATED_AT => Carbon::now()
            ],
            [
                AppType::NAME => 'Delivery',
                AppType::CREATED_AT => Carbon::now()
            ],
            [
                AppType::NAME => 'News',
                AppType::CREATED_AT => Carbon::now()
            ],
            [
                AppType::NAME => 'Charity',
                AppType::CREATED_AT => Carbon::now()
            ],
            [
                AppType::NAME => 'Massage',
                AppType::CREATED_AT => Carbon::now()
            ],
        ]);
    }
}
