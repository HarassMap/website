<?php

namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapDomainDomain extends Migration
{
    public function up()
    {
        Schema::create('harassmap_domain_domain', function($table)
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

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_domain_domain');
    }
}