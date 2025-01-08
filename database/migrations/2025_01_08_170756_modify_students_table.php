<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStudentsTable extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nickname')->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_nickname_unique');
        });
    }
}
