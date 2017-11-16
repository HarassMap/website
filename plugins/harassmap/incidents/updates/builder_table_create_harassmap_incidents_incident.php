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
            $table->string('public_id', 20);

            $table->text('description');
            $table->dateTime('date');

            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('domain_id')->unsigned();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // foreign keys
            $table->unique('public_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_incident');
    }
}