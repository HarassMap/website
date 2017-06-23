<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsDomainCategory extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_domain_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('category_id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->primary(['category_id','domain_id'], 'harassmap_incidents_domain_category_primary');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_domain_category');
    }
}