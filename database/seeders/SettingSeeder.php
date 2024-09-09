<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(GeneralSetting::TABLE_NAME)->truncate();

        DB::table(GeneralSetting::TABLE_NAME)->insert(
            [
                [
                    GeneralSetting::ID => 1,
                    GeneralSetting::KEY => 'WATER_MARK',
                    GeneralSetting::VALUE => '',
                    GeneralSetting::CREATED_AT => Carbon::now(),
                    GeneralSetting::UPDATED_AT => Carbon::now()
                ],
                [
                    GeneralSetting::ID => 2,
                    GeneralSetting::KEY => 'PROPERTY_TRANSACTION_FEE',
                    GeneralSetting::VALUE => 1,
                    GeneralSetting::CREATED_AT => Carbon::now(),
                    GeneralSetting::UPDATED_AT => Carbon::now()
                ],
                [
                    GeneralSetting::ID => 3,
                    GeneralSetting::KEY => 'TRANSACTION_PAYMENT_DEADLINE',
                    GeneralSetting::VALUE => Carbon::today(),
                    GeneralSetting::CREATED_AT => Carbon::now(),
                    GeneralSetting::UPDATED_AT => Carbon::now()
                ],
                [
                    GeneralSetting::ID => 4,
                    GeneralSetting::KEY => 'SECURITY_CODE',
                    GeneralSetting::VALUE => 1,
                    GeneralSetting::CREATED_AT => Carbon::now(),
                    GeneralSetting::UPDATED_AT => Carbon::now()
                ],
                [
                    GeneralSetting::ID => 5,
                    GeneralSetting::KEY => 'API_VERSION',
                    GeneralSetting::VALUE => '{"version":"2","min_version":"1.9"}',
                    GeneralSetting::CREATED_AT => Carbon::now(),
                    GeneralSetting::UPDATED_AT => Carbon::now()
                ]
            ]
        );
    }
}
