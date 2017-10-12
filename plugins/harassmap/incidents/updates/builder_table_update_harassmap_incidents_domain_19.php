<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain19 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->string('meta_description')->nullable();
            $table->dropColumn('about');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropColumn('meta_description');
            $table->string('about', 255);
        });
    }
}
