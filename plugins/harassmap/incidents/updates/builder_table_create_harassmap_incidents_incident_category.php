<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentCategory extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incident_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->smallInteger('incident_id')->unsigned();
            $table->smallInteger('category_id')->unsigned();
            $table->primary(['incident_id','category_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incident_category');
    }
}
