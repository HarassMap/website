<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsTip extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_tip', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('tip', 255);
            $table->dateTime('featured_from');
            $table->string('read_more', 50)->nullable();
            $table->string('link', 100)->nullable();

            $table->integer('domain_id')->unsigned();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_tip');
    }
}
