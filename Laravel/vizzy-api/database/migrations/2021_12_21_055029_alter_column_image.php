<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('favourited', function (Blueprint $table) {
            $table->string('image',500)->change();
            $table->string('feed_url',500)->change();
        });

        Schema::table('snippets', function (Blueprint $table) {
            $table->string('image',500)->change();
            $table->string('feed_url',500)->change();
        });

        Schema::table('played', function (Blueprint $table) {
            $table->string('image',500)->change();
            $table->string('feed_url',500)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('favourited', function (Blueprint $table) {
            $table->string('image')->change();
            $table->string('feed_url')->change();
        });

        Schema::table('snippets', function (Blueprint $table) {
            $table->string('image')->change();
            $table->string('feed_url')->change();
        });

        Schema::table('played', function (Blueprint $table) {
            $table->string('image')->change();
            $table->string('feed_url')->change();
        });
    }
}
