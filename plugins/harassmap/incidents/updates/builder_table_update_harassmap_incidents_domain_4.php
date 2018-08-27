<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain4 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->string('map_pin_color');
        });
    }

    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropColumn('map_pin_color');
        });
    }
}
