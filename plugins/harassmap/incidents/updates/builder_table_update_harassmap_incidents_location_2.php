<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsLocation2 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->decimal('lat', 10, 6)->nullable(false)->unsigned(false)->default(null)->change();
            $table->decimal('lng', 10, 6)->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->string('lat', 255)->nullable(false)->unsigned(false)->default(null)->change();
            $table->string('lng', 255)->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
