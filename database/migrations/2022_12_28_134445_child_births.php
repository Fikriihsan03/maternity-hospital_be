<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('child_births', function (Blueprint $table) {
            $table->id();
            $table->string('mother_name', 100);
            $table->string('mother_age');
            $table->integer('gestational_age');
            $table->string('baby_gender');
            $table->integer('baby_weight');
            $table->integer('baby_length');
            $table->string("birth_description");
            $table->string('birthing_method',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('child_births');
    }
};
