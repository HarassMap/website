<?php namespace Harassmap\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateFrontendUser extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->boolean('terms')->default(0);
            $table->boolean('marketing')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('terms');
            $table->dropColumn('marketing');
        });
    }
}