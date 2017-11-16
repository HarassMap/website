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
            $table->string('address', 255)->nullable();
            $table->decimal('lat', 10, 6)->nullable(false)->unsigned(false)->default(null);
            $table->decimal('lng', 10, 6)->nullable(false)->unsigned(false)->default(null);
            $table->integer('incident_id')->unsigned();

            $table->foreign('incident_id')->references('id')->on('harassmap_incidents_incident');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_location');
    }
}
