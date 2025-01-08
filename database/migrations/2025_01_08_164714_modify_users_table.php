<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id')->comment('用户名');
            $table->string('nickname')->unique()->after('username')->comment('昵称');
            $table->string('avatar')->after('nickname')->comment('头像')->nullable();
            $table->dropColumn('name');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('nickname');
            $table->dropColumn('avatar');
            $table->string('name')->after('id')->comment('名称');
        });
    }
}
