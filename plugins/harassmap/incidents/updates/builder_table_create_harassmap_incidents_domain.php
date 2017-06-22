<?php

namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsDomain extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_domain', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('host', 255);
            $table->string('about', 255);

            $table->string('facebook', 255)->nullable();
            $table->string('twitter', 255)->nullable();
            $table->string('instagram', 255)->nullable();
            $table->string('youtube', 255)->nullable();
            $table->string('blogger', 255)->nullable();
            $table->string('lat', 100);
            $table->string('lng', 100);
            $table->integer('zoom');
            $table->string('name', 100);
            $table->boolean('incident')->default(1);
            $table->boolean('intervention')->default(1);
            $table->integer('country_id')->unsigned()->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('country_id')->references('id')->on('harassmap_incidents_country');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_domain');
    }
}