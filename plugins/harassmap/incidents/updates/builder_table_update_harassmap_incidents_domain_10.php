<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain10 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->string('host', 50)->change();
            $table->string('facebook', 100)->change();
            $table->string('twitter', 100)->change();
            $table->string('instagram', 100)->change();
            $table->string('youtube', 100)->change();
            $table->string('blogger', 100)->change();
            $table->string('facebook_app_id', 32)->change();
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->string('host', 255)->change();
            $table->string('facebook', 255)->change();
            $table->string('twitter', 255)->change();
            $table->string('instagram', 255)->change();
            $table->string('youtube', 255)->change();
            $table->string('blogger', 255)->change();
            $table->string('facebook_app_id', 255)->change();
        });
    }
}
