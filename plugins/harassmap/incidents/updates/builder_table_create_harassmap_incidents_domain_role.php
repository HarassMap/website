<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsDomainRole extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_domain_role', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('role_id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->primary(['role_id','domain_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_domain_role');
    }
}
