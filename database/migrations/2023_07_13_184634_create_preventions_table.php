<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('preventions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('task');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('workers');
            $table->string('machine');
            $table->string('parts');
            $table->string('priority');
            $table->string('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventions');
    }
};
