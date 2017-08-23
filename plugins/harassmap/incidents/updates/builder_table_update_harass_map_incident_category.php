<?php namespace Harassmap\Incidents\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableUpdateHarassMapIncidentCategory extends Migration
{
    public function up()
    {
        Schema::dropIfExists('harassmap_incidents_incident_category');
        Schema::create('harassmap_incidents_incident_category', function ($table) {
            $table->engine = 'InnoDB';
            $table->integer('incident_id')->unsigned()->nullable();
            $table->foreign('incident_id', 'incident_id_foreign')->references('id')
                ->on('harassmap_incidents_incident')->onDelete('cascade');

            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id', 'category_id_foreign')->references('id')
                ->on('harassmap_incidents_category')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_incident_category');
    }
}