<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodcastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('feed_owner_email');
            $table->string('feed_url');
            $table->integer('episodes');
            $table->timestamps();
        });

        Schema::create('podcast_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->string('image');
            $table->string('feed_owner_email');
            $table->string('feed_url');
            $table->integer('episodes');
            $table->text('categories');
            $table->string('code');             
            $table->timestamps();
        });

        Schema::create('podcasts_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('podcast_id');
            $table->string('category');

            $table->foreign('podcast_id')->references('id')->on('podcasts')->onDelete('cascade');
            
            $table->primary(['podcast_id','category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('podcasts_categories');
        Schema::dropIfExists('podcasts');
        Schema::dropIfExists('podcast_verifications');
    }
}
