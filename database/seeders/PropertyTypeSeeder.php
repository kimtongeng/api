<?php

namespace Database\Seeders;

use App\Enums\Types\PropertyTypeEnum;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(PropertyType::TABLE_NAME)->truncate();

        DB::table(PropertyType::TABLE_NAME)->insert([
            [
                PropertyType::NAME => '{"local_name": "ដីឡូត៍ (គម្រោង)", "latin_name": "Land (Project)"}',
                PropertyType::TYPE => PropertyTypeEnum::getMulti(),
                PropertyType::IMAGE => 'land_project.svg',
                PropertyType::CREATED_AT => Carbon::now()
            ],
            [
                PropertyType::NAME => '{"local_name": "បុរី (គម្រោង)", "latin_name": "Borey (Project)"}',
                PropertyType::TYPE => PropertyTypeEnum::getMulti(),
                PropertyType::IMAGE => 'borey_project.svg',
                PropertyType::CREATED_AT => Carbon::now()
            ],
            [
                PropertyType::NAME => '{"local_name": "ខុនដូ (គម្រោង)", "latin_name": "Condo (Project)"}',
                PropertyType::TYPE => PropertyTypeEnum::getMulti(),
                PropertyType::IMAGE => 'condo_project.svg',
                PropertyType::CREATED_AT => Carbon::now()
            ],
            [
                PropertyType::NAME => '{"local_name": "ផ្ទះ", "latin_name": "House"}',
                PropertyType::TYPE => PropertyTypeEnum::getSingle(),
                PropertyType::IMAGE => 'house.svg',
                PropertyType::CREATED_AT => Carbon::now()
            ],
            [
                PropertyType::NAME => '{"local_name": "ដី", "latin_name": "Land"}',
                PropertyType::TYPE => PropertyTypeEnum::getSingle(),
                PropertyType::IMAGE => 'land.svg',
                PropertyType::CREATED_AT => Carbon::now()
            ],
            [
                PropertyType::NAME => '{"local_name": "ពាណិជ្ជកម្ម", "latin_name": "Commercial"}',
                PropertyType::TYPE => PropertyTypeEnum::getSingle(),
                PropertyType::IMAGE => 'commercial.svg',
                PropertyType::CREATED_AT => Carbon::now()
            ],
        ]);
    }
}
