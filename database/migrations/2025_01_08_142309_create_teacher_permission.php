<?php

use Illuminate\Database\Migrations\Migration;

class CreateTeacherPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('admin_permissions')->insert([
            'name' => '教师管理',
            'slug' => 'teacher',
            'http_method' => '',
            'http_path' => '/teachers*',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('admin_permissions')->where('slug', 'teacher')->delete();
    }
}
