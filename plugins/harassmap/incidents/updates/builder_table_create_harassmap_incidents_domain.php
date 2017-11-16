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
            $table->string('host', 50);

            $table->string('facebook', 100)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->string('instagram', 100)->nullable();
            $table->string('youtube', 100)->nullable();
            $table->string('blogger', 100)->nullable();
            $table->string('lat', 100);
            $table->string('lng', 100);
            $table->integer('zoom');
            $table->string('name', 100);
            $table->boolean('incident')->default(1);
            $table->boolean('intervention')->default(1);
            $table->string('facebook_app_id', 32)->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->string('tagline', 100);
            $table->string('nameend', 100);
            $table->string('ga_key', 40)->nullable();
            $table->string('twitter_message', 100)->nullable();
            $table->boolean('need_approval')->default(1);
            $table->text('colours')->nullable();
            $table->text('logos')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('languages', 100)->nullable();
            $table->string('default_language', 10)->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_domain');
    }
}