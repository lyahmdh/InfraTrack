<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{

    public function up(): void
    {
        DB::statement("
            ALTER TABLE reports 
            ADD coordinates POINT NULL
        ");

        DB::statement("
            CREATE SPATIAL INDEX coordinates_index 
            ON reports (coordinates)
        ");
    }

    public function down(): void
    {
        DB::statement("
            DROP INDEX coordinates_index ON reports
        ");

        DB::statement("
            ALTER TABLE reports DROP COLUMN coordinates
        ");
    }
};
