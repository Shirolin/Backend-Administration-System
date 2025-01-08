<?php

use Illuminate\Database\Migrations\Migration;

class CreateTeacherRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('admin_roles')->insert([
            'name' => 'Teacher',
            'slug' => 'teacher',
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
        DB::table('admin_roles')->where('slug', 'teacher')->delete();
    }
}
