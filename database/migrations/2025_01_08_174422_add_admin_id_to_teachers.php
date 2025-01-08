<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminIdToTeachers extends Migration
{
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->integer('admin_id')->after('id')->comment('管理员ID');

            // 添加外键约束
            $table->foreign('admin_id')
                ->references('id')
                ->on('admin_users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign('teachers_admin_id_foreign');
            $table->dropColumn('admin_id');
        });
    }
}
