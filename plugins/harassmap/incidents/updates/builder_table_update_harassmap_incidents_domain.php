<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->string('facebook_app_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropColumn('facebook_app_id');
        });
    }
}
