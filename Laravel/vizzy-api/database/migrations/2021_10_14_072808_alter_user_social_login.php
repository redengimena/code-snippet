<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserSocialLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('provider_name')->nullable()->after('remember_token');
            $table->string('provider_id')->nullable()->after('provider_name');
            $table->string('image')->nullable()->after('provider_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
          $table->dropColumn('image');
          $table->dropColumn('provider_id');
          $table->dropColumn('provider_name');
          $table->string('password')->change();
          $table->string('email')->change();
        });
    }
}
