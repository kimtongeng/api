<?php

namespace Database\Seeders;

use App\Models\PrefixCode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrefixCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(PrefixCode::TABLE_NAME)->truncate();

        DB::table(PrefixCode::TABLE_NAME)->insert([
            [
                PrefixCode::TYPE => PrefixCode::PROPERTY,
                PrefixCode::PREFIX => 'PR',
                PrefixCode::CODE_LENGTH => 6,
                PrefixCode::CREATED_AT => Carbon::now(),
                PrefixCode::UPDATED_AT => Carbon::now()
            ],
            [
                PrefixCode::TYPE => PrefixCode::ASSET,
                PrefixCode::PREFIX => 'AS',
                PrefixCode::CODE_LENGTH => 6,
                PrefixCode::CREATED_AT => Carbon::now(),
                PrefixCode::UPDATED_AT => Carbon::now()
            ],
            [
                PrefixCode::TYPE => PrefixCode::TRANSACTION,
                PrefixCode::PREFIX => 'TRAN',
                PrefixCode::CODE_LENGTH => 6,
                PrefixCode::CREATED_AT => Carbon::now(),
                PrefixCode::UPDATED_AT => Carbon::now()
            ],
            [
                PrefixCode::TYPE => PrefixCode::CONTACT,
                PrefixCode::PREFIX => 'SP',
                PrefixCode::CODE_LENGTH => 8,
                PrefixCode::CREATED_AT => Carbon::now(),
                PrefixCode::UPDATED_AT => Carbon::now()
            ],
            [
                PrefixCode::TYPE => PrefixCode::AGENCY,
                PrefixCode::PREFIX => 'AG',
                PrefixCode::CODE_LENGTH => 8,
                PrefixCode::CREATED_AT => Carbon::now(),
                PrefixCode::UPDATED_AT => Carbon::now()
            ],
            [
                PrefixCode::TYPE => PrefixCode::DELIVERY,
                PrefixCode::PREFIX => 'DEL',
                PrefixCode::CODE_LENGTH => 6,
                PrefixCode::CREATED_AT => Carbon::now(),
                PrefixCode::UPDATED_AT => Carbon::now()
            ],
        ]);
    }
}
