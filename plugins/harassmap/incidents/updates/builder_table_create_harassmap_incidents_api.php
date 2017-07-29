<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsApi extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_api', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('key', 20);
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('calls');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_api');
    }
}