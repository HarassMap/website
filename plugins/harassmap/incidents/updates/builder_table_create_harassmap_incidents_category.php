<?php

namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsCategory extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 255);
            $table->text('description');
            $table->integer('sort_order');
            $table->integer('domain_id')->unsigned()->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_category');
    }
}