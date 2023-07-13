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
        Schema::create('machines', function (Blueprint $table) {
            $table->id()->unique();
            $table->timestamps();
            $table->string('code');
            $table->string('name');
            $table->string('type');
            $table->string('serie');
            $table->string('section');
            $table->string('unit');
            $table->string('supplier');
            $table->date('purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
