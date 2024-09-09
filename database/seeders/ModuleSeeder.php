<?php

namespace Database\Seeders;

use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Module::TABLE_NAME)->insert([
            [
                Module::MODULE_NAME => 'Dashboard',
                Module::MODULE_KEY => 'dashboard',
                Module::FEATURED => 1,
                Module::SEQUENCE => 1,
                Module::PERMISSION => '4',
                Module::MENU_TITLE => '',
                Module::CREATED_AT => Carbon::now(),
                Module::UPDATED_AT => Carbon::now()
            ],
            [
                Module::MODULE_NAME => 'Contact',
                Module::MODULE_KEY => 'contact',
                Module::FEATURED => 0,
                Module::SEQUENCE => 130,
                Module::PERMISSION => '2,4',
                Module::MENU_TITLE => '',
                Module::CREATED_AT => Carbon::now(),
                Module::UPDATED_AT => Carbon::now()
            ],
            [
                Module::MODULE_NAME => 'User Role',
                Module::MODULE_KEY => 'user_role',
                Module::SEQUENCE => 140,
                Module::PERMISSION => '1,2,3,4',
                Module::MENU_TITLE => 'user_management',
                Module::FEATURED => 0,
                Module::CREATED_AT => Carbon::now(),
                Module::UPDATED_AT => Carbon::now()
            ],
            [
                Module::MODULE_NAME => 'User List',
                Module::MODULE_KEY => 'user_list',
                Module::SEQUENCE => 150,
                Module::PERMISSION => '1,2,3,4',
                Module::MENU_TITLE => '',
                Module::FEATURED => 0,
                Module::CREATED_AT => Carbon::now(),
                Module::UPDATED_AT => Carbon::now()
            ],
            [
                Module::MODULE_NAME => 'User Log',
                Module::MODULE_KEY => 'user_log',
                Module::SEQUENCE => 160,
                Module::PERMISSION => '1,2,3,4',
                Module::MENU_TITLE => '',
                Module::FEATURED => 0,
                Module::CREATED_AT => Carbon::now(),
                Module::UPDATED_AT => Carbon::now()
            ],
        ]);
    }
}
