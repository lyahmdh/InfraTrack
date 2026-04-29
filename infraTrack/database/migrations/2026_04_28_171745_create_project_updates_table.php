<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_updates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')
                ->constrained()
                ->cascadeOnDelete();

            // admin yang membuat update
            $table->foreignId('admin_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title', 150);

            $table->text('description');

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_updates');
    }
};