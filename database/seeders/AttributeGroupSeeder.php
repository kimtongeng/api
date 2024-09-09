<?php

namespace Database\Seeders;

use App\Enums\Types\BusinessTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AttributeGroup;
use Carbon\Carbon;

class AttributeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(AttributeGroup::TABLE_NAME)->truncate();

        DB::table(AttributeGroup::TABLE_NAME)->insert([
            [
                AttributeGroup::BUSINESS_TYPE_ID => BusinessTypeEnum::getAccommodation(),
                AttributeGroup::NAME => '{"local_name": "គ្រឿងបរិក្ខារ", "latin_name": "Facilities"}',
                AttributeGroup::KEY => 'facilities',
                AttributeGroup::CREATED_AT => Carbon::now(),
            ],
        ]);
    }
}
