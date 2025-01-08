<?php

use Illuminate\Database\Migrations\Migration;

class CreateStudentPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('admin_permissions')->insert([
            'name' => '学生管理',
            'slug' => 'student',
            'http_method' => '',
            'http_path' => '/students*',
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
        DB::table('admin_permissions')->where('slug', 'student')->delete();
    }
}
