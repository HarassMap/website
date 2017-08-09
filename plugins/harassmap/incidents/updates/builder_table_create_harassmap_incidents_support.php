<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsSupport extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_support', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('incident_id')->unsigned();
            $table->integer('count')->unsigned();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('incident_id')->references('id')->on('harassmap_incidents_incident');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_support');
    }
}