<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('my_favorite_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->text('description');
            $table->string('element_type');
            $table->integer('power_level');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('my_favorite_subjects');
    }
}; 