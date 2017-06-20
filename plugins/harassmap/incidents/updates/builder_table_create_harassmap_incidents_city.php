<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsCity extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_city', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('lat', 255);
            $table->string('lng', 255);
            $table->smallInteger('country_id')->unsigned();

            $table->foreign('country_id')->references('id')->on('harassmap_incidents_country');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_city');
    }
}
