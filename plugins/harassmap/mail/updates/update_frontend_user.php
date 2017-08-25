<?php namespace Harassmap\Mail\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateFrontendUser extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('locale', 10)->default('en');
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('terms');
        });
    }
}