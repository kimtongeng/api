<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PrefixCodeSeeder::class,
            PermissionSeeder::class,
            UserTypeSeeder::class,
            ModuleSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SupportSeeder::class,
            SettingSeeder::class,
            AppTypeSeeder::class,
            BusinessTypeSeeder::class,
            DocumentTypeSeeder::class,
            PropertyTypeSeeder::class,
            BankSeeder::class,
            BusinessPermissionSeeder::class,
            AttributeGroupSeeder::class,
            AppCountrySeeder::class,
        ]);
    }
}
