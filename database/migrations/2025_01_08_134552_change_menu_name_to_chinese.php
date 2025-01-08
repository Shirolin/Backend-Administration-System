<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMenuNameToChinese extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('admin_menu')->where('id', 1)->update(['title' => '仪表盘']);
        DB::table('admin_menu')->where('id', 2)->update(['title' => '管理']);
        DB::table('admin_menu')->where('id', 3)->update(['title' => '用户']);
        DB::table('admin_menu')->where('id', 4)->update(['title' => '角色']);
        DB::table('admin_menu')->where('id', 5)->update(['title' => '权限']);
        DB::table('admin_menu')->where('id', 6)->update(['title' => '菜单']);
        DB::table('admin_menu')->where('id', 7)->update(['title' => '操作日志']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('admin_menu')->where('id', 1)->update(['title' => 'Dashboard']);
        DB::table('admin_menu')->where('id', 2)->update(['title' => 'Admin']);
        DB::table('admin_menu')->where('id', 3)->update(['title' => 'Users']);
        DB::table('admin_menu')->where('id', 4)->update(['title' => 'Roles']);
        DB::table('admin_menu')->where('id', 5)->update(['title' => 'Permission']);
        DB::table('admin_menu')->where('id', 6)->update(['title' => 'Menu']);
        DB::table('admin_menu')->where('id', 7)->update(['title' => 'Operation log']);
    }
}
