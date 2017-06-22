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
            $table->increments('id')->unsigned();
            $table->string('city');
            $table->string('region');
            $table->string('address');
            $table->string('lat');
            $table->string('lng');
            $table->integer('country_id')->unsigned();

            $table->foreign('country_id')->references('id')->on('harassmap_incidents_country');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_location');
    }
}
