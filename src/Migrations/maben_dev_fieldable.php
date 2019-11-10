<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MabenDevFieldable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('MabenDevFieldable.database.prefix') . 'fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fieldable_model');
            $table->string('name')->unique();
            $table->string('type');
            $table->timestamps();
        });

        Schema::create(config('MabenDevFieldable.database.prefix') . 'field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fieldable_id');
            $table->unsignedBigInteger('field_id');
            $table->text('value');
            $table->boolean('read-only')->default(false);
            $table->timestamps();

            $table->foreign('field_id')
                ->references('id')
                ->on(config('MabenDevFieldable.database.prefix') . 'fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('MabenDevFieldable.database.prefix') . 'fields');
        Schema::dropIfExists(config('MabenDevFieldable.database.prefix') . 'values');
    }
}
