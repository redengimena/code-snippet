<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVizzyStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vizzies', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('content');
            $table->timestamp('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vizzies', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('published_at');
        });
    }
}
