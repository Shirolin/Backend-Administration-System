<?php

use Illuminate\Database\Migrations\Migration;

class CreateTeacherMenu extends Migration
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
            'order' => 8,
            'title' => '教师管理',
            'icon' => 'fa-user',
            'uri' => 'teachers',
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
        DB::table('admin_menu')->where('uri', 'teachers')->delete();
    }
}
