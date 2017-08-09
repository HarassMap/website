<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsRole extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_role', function($table)
        {
            $table->string('name', 100)->change();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_role', function($table)
        {
            $table->string('name', 255)->change();
        });
    }
}
