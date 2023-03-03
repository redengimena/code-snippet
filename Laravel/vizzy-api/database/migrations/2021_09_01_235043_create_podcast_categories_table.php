<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodcastCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('podcast_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');            
            $table->timestamps();
        });

        Schema::create('podcast_category_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('podcast_category_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('podcast_category_id')->references('id')->on('podcast_categories')->onDelete('cascade');
        });
        
        Schema::create('user_podcast_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('podcast_category_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('podcast_category_id')->references('id')->on('podcast_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_podcast_categories');
        Schema::dropIfExists('podcast_category_mappings');
        Schema::dropIfExists('podcast_categories');
    }
}
