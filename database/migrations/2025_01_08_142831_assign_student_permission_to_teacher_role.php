<?php

use Illuminate\Database\Migrations\Migration;

class AssignStudentPermissionToTeacherRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = DB::table('admin_roles')->where('slug', 'teacher')->first();
        $permission = DB::table('admin_permissions')->where('slug', 'student')->first();
        DB::table('admin_role_permissions')->insert([
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = DB::table('admin_roles')->where('slug', 'teacher')->first();
        $permission = DB::table('admin_permissions')->where('slug', 'student')->first();
        DB::table('admin_role_permissions')->where('role_id', $role->id)->where('permission_id', $permission->id)->delete();
    }
}
