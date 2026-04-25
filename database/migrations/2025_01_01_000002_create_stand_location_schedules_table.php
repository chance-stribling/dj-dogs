<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stand_location_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stand_location_id')
                ->constrained('stand_locations')
                ->cascadeOnDelete();
            $table->date('date');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('until_sold_out')->default(false);
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stand_location_schedules');
    }
};
