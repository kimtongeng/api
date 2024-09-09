<?php

namespace Database\Seeders;

use App\Models\Bank;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(Bank::TABLE_NAME)->truncate();

        DB::table(Bank::TABLE_NAME)->insert([
            [
                Bank::NAME => 'ABA Bank',
                Bank::IMAGE => 'aba_bank.jpg',
                Bank::CREATED_AT => Carbon::now()
            ],
            [
                Bank::NAME => 'Wing Bank',
                Bank::IMAGE => 'wing_bank.jpg',
                Bank::CREATED_AT => Carbon::now()
            ],
            [
                Bank::NAME => 'True Money',
                Bank::IMAGE => 'true_money.jpg',
                Bank::CREATED_AT => Carbon::now()
            ],
            [
                Bank::NAME => 'ACLEDA Bank',
                Bank::IMAGE => 'acleda_bank.jpg',
                Bank::CREATED_AT => Carbon::now()
            ],
        ]);
    }
}
