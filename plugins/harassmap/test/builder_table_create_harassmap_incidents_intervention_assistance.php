<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassMapInterventionAssistance extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_intervention_assistance', function($table)
        {
            $table->engine = 'InnoDB';
            $table->smallInteger('intervention_id')->unsigned();
            $table->smallInteger('assistance_id')->unsigned();
            $table->primary(['intervention_id','assistance_id'], 'harassmap_incidents_intervention_assistance_primary');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_intervention_assistance');
    }
}
