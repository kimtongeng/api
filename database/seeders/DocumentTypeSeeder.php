<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(DocumentType::TABLE_NAME)->truncate();

        DB::table(DocumentType::TABLE_NAME)->insert([
            [
                DocumentType::NAME => 'ID No',
                DocumentType::CREATED_AT => Carbon::now()
            ],
            [
                DocumentType::NAME => 'Passport',
                DocumentType::CREATED_AT => Carbon::now()
            ],
            [
                DocumentType::NAME => 'Land Title',
                DocumentType::CREATED_AT => Carbon::now()
            ],
            [
                DocumentType::NAME => 'Business License',
                DocumentType::CREATED_AT => Carbon::now()
            ],
        ]);
    }
}
