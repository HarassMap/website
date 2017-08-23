<?php namespace Harassmap\Incidents\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableUpdateHarassMapInterventionAssistance extends Migration
{
    public function up()
    {
        Schema::dropIfExists('harassmap_incidents_intervention_assistance');
        Schema::create('harassmap_incidents_intervention_assistance', function ($table) {

            $table->engine = 'InnoDB';
            $table->integer('intervention_id')->unsigned()->nullable();
            $table->foreign('intervention_id', 'intervention_id_foreign')->references('id')
                ->on('harassmap_incidents_intervention')->onDelete('cascade');

            $table->integer('assistance_id')->unsigned()->nullable();
            $table->foreign('assistance_id', 'assistance_id_foreign')->references('id')
                ->on('harassmap_incidents_assistance')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_intervention_assistance');
    }
}