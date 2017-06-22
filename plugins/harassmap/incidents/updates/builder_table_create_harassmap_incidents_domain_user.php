<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsDomainUser extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_domain_user', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->primary(['user_id','domain_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_domain_user');
    }
}
