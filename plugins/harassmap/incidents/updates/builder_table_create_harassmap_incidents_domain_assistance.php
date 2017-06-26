<?php namespace Harassmap\Incidents\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableCreateHarassmapIncidentsDomainAssistance extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_domain_assistance', function ($table) {
            $table->engine = 'InnoDB';
            $table->integer('assistance_id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->primary(['assistance_id', 'domain_id'], 'harassmap_incidents_domain_assistance_primary');
        });
    }

    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_domain_assistance');
    }
}