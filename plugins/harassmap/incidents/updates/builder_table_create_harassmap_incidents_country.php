<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsCountry extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_country', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 100);
            $table->string('iso', 10);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_country');
    }
}
