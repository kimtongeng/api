<?php

namespace Database\Seeders;

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Permission::TABLE_NAME)->truncate();

        DB::table(Permission::TABLE_NAME)->insert([
            [
                Permission::PERMISSION_NAME => 'create',
                Permission::CREATED_AT => Carbon::now(),
                Permission::UPDATED_AT => Carbon::now(),
                Permission::MODULE_ID => 1
            ],
            [
                Permission::PERMISSION_NAME => 'update',
                Permission::CREATED_AT => Carbon::now(),
                Permission::UPDATED_AT => Carbon::now(),
                Permission::MODULE_ID => 1
            ],
            [
                Permission::PERMISSION_NAME => 'delete',
                Permission::CREATED_AT => Carbon::now(),
                Permission::UPDATED_AT => Carbon::now(),
                Permission::MODULE_ID => 1
            ],
            [
                Permission::PERMISSION_NAME => 'view',
                Permission::CREATED_AT => Carbon::now(),
                Permission::UPDATED_AT => Carbon::now(),
                Permission::MODULE_ID => 1
            ],
        ]);
    }
}
