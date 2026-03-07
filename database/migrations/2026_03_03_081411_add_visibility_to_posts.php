<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_visibility_to_posts.php
        public function up()
        {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('visibility')->default('public')->after('content'); // public / private
            });
        }

        public function down()
        {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('visibility');
            });
        }
};
