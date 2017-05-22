<?php namespace Harassmap\Incidents\Updates;

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
            $table->string('color', 20);
            $table->integer('sort_order');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_category');
    }
}
