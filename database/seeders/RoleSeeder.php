<?php

namespace Database\Seeders;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Role::TABLE_NAME)->truncate();

        DB::table(Role::TABLE_NAME)->insert(
            [
                Role::ID => 1,
                Role::ROLE_NAME => 'super',
                Role::ROLE_DESC => 'Role for IDG',
                Role::USER_TYPE_ID => 1,
                Role::CREATED_AT => Carbon::now(),
                Role::UPDATED_AT => Carbon::now()
            ]
        );
    }
}
