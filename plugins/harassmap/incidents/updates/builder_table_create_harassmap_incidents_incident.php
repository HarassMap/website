<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsIncident extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_incident', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('public_id', '100');

            $table->text('description');
            $table->dateTime('date');

            $table->integer('user_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->integer('domain_id')->unsigned();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
            $table->foreign('location_id')->references('id')->on('harassmap_incidents_location');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_incident');
    }
}