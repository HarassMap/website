<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsDomainLogo extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_domain_logo', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->string('language', 10);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
            $table->unique(['domain_id', 'language']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_domain_logo');
    }
}