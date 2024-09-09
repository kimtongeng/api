<?php

namespace Database\Seeders;

use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(UserType::TABLE_NAME)->truncate();

        DB::table(UserType::TABLE_NAME)->insert([
            [
                UserType::TYPE => 'IDG',
                UserType::LEVEL => 7,
                UserType::CREATED_AT => Carbon::now(),
                UserType::UPDATED_AT => Carbon::now()
            ],
            [
                UserType::TYPE => 'Super Admin',
                UserType::LEVEL => 6,
                UserType::CREATED_AT => Carbon::now(),
                UserType::UPDATED_AT => Carbon::now()
            ],
            [
                UserType::TYPE => 'User',
                UserType::LEVEL => 3,
                UserType::CREATED_AT => Carbon::now(),
                UserType::UPDATED_AT => Carbon::now()
            ]
        ]);
    }
}
