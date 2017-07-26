<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteHarassmapIncidentsCity extends Migration
{
    public function up()
    {
        Schema::dropIfExists('harassmap_incidents_city');
    }
    
    public function down()
    {
        Schema::create('harassmap_incidents_city', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('country_id')->unsigned();
            $table->string('name', 255);
            $table->decimal('lat', 10, 6);
            $table->decimal('lng', 10, 6);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
}
