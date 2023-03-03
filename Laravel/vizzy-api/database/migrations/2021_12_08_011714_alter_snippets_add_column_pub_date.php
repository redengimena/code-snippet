<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSnippetsAddColumnPubDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snippets', function (Blueprint $table) {
            $table->string('type')->nullable()->after('user_id');
            $table->string('show_name')->nullable()->after('episode_guid');
            $table->string('episode_name')->nullable()->after('show_name');
            $table->string('image')->nullable()->after('episode_name');
            $table->timestamp('pub_date')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snippets', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('show_name');
            $table->dropColumn('episode_name');
            $table->dropColumn('image');
            $table->dropColumn('pub_date');
        });
    }
}
