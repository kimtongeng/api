<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(User::TABLE_NAME)->truncate();

        DB::table(User::TABLE_NAME)->insert([
            User::ID => 1,
            User::USERNAME => 'idg',
            User::FULL_NAME => 'IDG',
            User::EMAIL => 'admin@gmail.com',
            User::PASSWORD => Hash::make('$idg168$'),
            User::ROLE_ID => 1,
            User::USER_TYPE_ID => 1,
            User::CREATED_AT => Carbon::now(),
            User::UPDATED_AT => Carbon::now()
        ]);
    }
}
