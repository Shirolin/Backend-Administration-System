<?php

use Illuminate\Database\Migrations\Migration;

class CreateStudentMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('admin_menu')->insert([
            'parent_id' => 0,
            'order' => 9,
            'title' => '学生管理',
            'icon' => 'fa-user',
            'uri' => 'students',
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
        DB::table('admin_menu')->where('uri', 'students')->delete();
    }
}
