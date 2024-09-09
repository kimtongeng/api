<?php

namespace Database\Seeders;

use App\Enums\Types\BusinessTypeEnum;
use App\Models\BusinessAgencyType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessAgencyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(BusinessAgencyType::TABLE_NAME)->truncate();

        DB::table(BusinessAgencyType::TABLE_NAME)->insert([
            [
                BusinessAgencyType::BUSINESS_TYPE_ID => BusinessTypeEnum::getProperty(),
                BusinessAgencyType::NAME => '{"local_name":"ភ្នាក់ងារអចលនទ្រព្យ","latin_name":"Property Agency"}',
                BusinessAgencyType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessAgencyType::BUSINESS_TYPE_ID => BusinessTypeEnum::getMassage(),
                BusinessAgencyType::NAME => '{"local_name":"អ្នកម៉ាស្សា","latin_name":"Massage Therapist"}',
                BusinessAgencyType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessAgencyType::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessAgencyType::NAME => '{"local_name":"ខារ៉ាអូខេ","latin_name":"KTV"}',
                BusinessAgencyType::CREATED_AT => Carbon::now()
            ],
            [
                BusinessAgencyType::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                BusinessAgencyType::NAME => '{"local_name":"អ្នកបើកបរ","latin_name":"Driver"}',
                BusinessAgencyType::CREATED_AT => Carbon::now()
            ],
        ]);
    }
}
