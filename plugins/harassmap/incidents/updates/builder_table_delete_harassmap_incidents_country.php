<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteHarassmapIncidentsCountry extends Migration
{
    public function up()
    {
        Schema::dropIfExists('harassmap_incidents_country');
    }
    
    public function down()
    {
        Schema::create('harassmap_incidents_country', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 100);
            $table->string('iso', 10);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
}
