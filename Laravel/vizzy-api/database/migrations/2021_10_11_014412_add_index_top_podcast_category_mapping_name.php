<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexTopPodcastCategoryMappingName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('podcast_category_mappings', function(Blueprint $table)
        {
            $table->index('name');
        });

        Schema::table('podcast_categories', function(Blueprint $table)
        {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('podcast_category_mappings', function (Blueprint $table)
        {
            $table->dropIndex(['name']);
        });

        Schema::table('podcast_categories', function(Blueprint $table)
        {
            $table->index('name');
        });
    }
}
