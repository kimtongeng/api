<?php

namespace Database\Seeders;

use App\Models\Support;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Support::TABLE_NAME)->truncate();

        DB::table(Support::TABLE_NAME)->insert([
            [
                Support::SUPPORT_TYPE => 'PHONE_3',
                Support::SUPPORT_VALUE => '012 xxx xxx',
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'PHONE_2',
                Support::SUPPORT_VALUE => '096 xxx xxx',
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'PHONE_1',
                Support::SUPPORT_VALUE => '097 xxx xxx',
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'EMAIL',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'FACEBOOK',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'INSTAGRAM',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'TELEGRAM',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'YOUTUBE',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'LINE',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'MESSENGER',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'CHAT',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ],
            [
                Support::SUPPORT_TYPE => 'ADDRESS',
                Support::SUPPORT_VALUE => null,
                Support::CREATED_BY => 1,
                Support::UPDATED_BY => 1,
                Support::CREATED_AT => Carbon::now()
            ]
        ]);
    }
}
