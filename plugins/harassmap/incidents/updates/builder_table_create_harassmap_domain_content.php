<?php

namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapDomainContent extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_content', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->string('content_id');
            $table->text('content');
            $table->string('link', 255)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_content');
    }
}