<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsContent extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_content', function($table)
        {
            $table->string('content_id', 50)->change();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_content', function($table)
        {
            $table->string('content_id', 255)->change();
        });
    }
}
