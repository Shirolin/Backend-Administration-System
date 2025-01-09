<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvatarToTeachers extends Migration
{
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('avatar')->after('nickname')->comment('头像')->nullable();
        });
    }

    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
}
