<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsLocation5 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->string('address', 255)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->string('address', 255)->nullable(false)->change();
        });
    }
}