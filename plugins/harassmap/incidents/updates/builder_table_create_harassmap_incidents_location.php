<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsLocation extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_location', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('city', 255);
            $table->string('region', 255);
            $table->string('address', 255);
            $table->string('lat', 100);
            $table->string('lng', 100);

            $table->integer('country_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_location');
    }
}
