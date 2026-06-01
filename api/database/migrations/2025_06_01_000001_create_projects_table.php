<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client');
            $table->string('technology');
            $table->date('start_date');
            $table->string('status')->default('active');
            $table->string('priority')->default('medium');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('value')->default('$0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
