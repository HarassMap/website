<?php namespace Harassmap\Incidents\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableCreateHarassmapIncidentsApi extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_api', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('key', 40);
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('calls')->unsigned()->default(0);
            $table->integer('total')->unsigned()->default(0);
            $table->dateTime('last_call')->nullable();

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