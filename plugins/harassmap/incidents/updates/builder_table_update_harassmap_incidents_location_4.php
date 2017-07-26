<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsLocation4 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->dropForeign('harassmap_incidents_location_country_id_foreign');
            $table->dropColumn('country_id');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_location', function($table)
        {
            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('harassmap_incidents_country');
        });
    }
}