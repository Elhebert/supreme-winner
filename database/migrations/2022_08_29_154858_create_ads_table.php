<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();

            $table->text('body');
            $table->json('attributes');

            $table->integer('index');
            $table->bigInteger('bgg_id');
            $table->string('title');
            $table->string('author');
            $table->string('object_id');
            $table->string('expansions');
            $table->string('condition');
            $table->text('condition_comment')->nullable();
            $table->string('version');
            $table->string('language');
            $table->integer('starting_bid');
            $table->integer('bin');
            $table->integer('soft_reserve');
            $table->boolean('deleted');

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
        Schema::dropIfExists('ads');
    }
};
