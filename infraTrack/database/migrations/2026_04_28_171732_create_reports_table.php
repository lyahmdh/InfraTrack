<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title', 150);

            $table->text('description');

            // 1 ringan, 2 sedang, 3 berat
            $table->tinyInteger('severity');

            $table->boolean('is_anonymous')
                ->default(false);

            $table->enum('status', [
                'masuk',
                'diverifikasi',
                'ditindaklanjuti',
                'selesai',
                'ditolak'
            ])->default('masuk');

            $table->string('location_name');

            $table->string('district');

            $table->decimal('latitude', 10, 8);

            $table->decimal('longitude', 11, 8);

            $table->decimal('priority_score', 5, 2)
                ->default(0);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};