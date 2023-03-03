<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVizzyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vizzies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('podcast_id');
            $table->string('episode_guid')->index();
            $table->string('title');
            $table->string('image')->nullable();
            $table->json('content');
            $table->timestamps();

            $table->foreign('podcast_id')->references('id')->on('podcasts')->onDelete('cascade');
        });

        Schema::create('vizzy_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vizzy_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('image');
            $table->integer('start');
            $table->integer('end')->nullable();            

            $table->foreign('vizzy_id')->references('id')->on('vizzies')->onDelete('cascade');
        });

        Schema::create('interaction_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->text('content')->nullable();

            $table->foreign('card_id')->references('id')->on('vizzy_cards')->onDelete('cascade');
        });

        Schema::create('interaction_social_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->string('title')->nullable(); 

            $table->foreign('card_id')->references('id')->on('vizzy_cards')->onDelete('cascade');
        });

        Schema::create('interaction_social_group_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('url');

            $table->foreign('group_id')->references('id')->on('interaction_social_groups')->onDelete('cascade');
        });

        Schema::create('interaction_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->text('content')->nullable();
            $table->text('url');

            $table->foreign('card_id')->references('id')->on('vizzy_cards')->onDelete('cascade');
        });

        Schema::create('interaction_webs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->text('content')->nullable();            

            $table->foreign('card_id')->references('id')->on('vizzy_cards')->onDelete('cascade');
        });

        Schema::create('interaction_web_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('url');

            $table->foreign('group_id')->references('id')->on('interaction_webs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interaction_web_links');
        Schema::dropIfExists('interaction_webs');
        Schema::dropIfExists('interaction_products');
        Schema::dropIfExists('interaction_social_group_links');
        Schema::dropIfExists('interaction_social_groups');
        Schema::dropIfExists('interaction_infos');
        Schema::dropIfExists('vizzy_cards');
        Schema::dropIfExists('vizzies');        
    }
}
