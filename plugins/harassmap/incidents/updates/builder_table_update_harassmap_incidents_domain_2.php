<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain2 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->boolean('news_enabled')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropColumn('news_enabled');
        });
    }
}
