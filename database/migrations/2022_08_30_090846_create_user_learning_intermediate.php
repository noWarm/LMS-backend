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
        Schema::create('user_learning_intermediate', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('course_id')
            ->constrained('courses')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();

            $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_learning_intermediate');
    }
};
