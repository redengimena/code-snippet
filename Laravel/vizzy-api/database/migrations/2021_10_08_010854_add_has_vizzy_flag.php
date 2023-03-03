<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasVizzyFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('played', function (Blueprint $table) {
            $table->boolean('has_vizzy')->default(false)->after('episode_guid');
        });

        Schema::table('favourited', function (Blueprint $table) {
            $table->boolean('has_vizzy')->default(false)->after('episode_guid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('played', function (Blueprint $table) {
            $table->dropColumn('has_vizzy');
        });

        Schema::table('favourited', function (Blueprint $table) {
            $table->dropColumn('has_vizzy');
        });
    }
}
