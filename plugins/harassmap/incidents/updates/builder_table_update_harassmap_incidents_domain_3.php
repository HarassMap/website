<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsDomain3 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropForeign('harassmap_incidents_domain_country_id_foreign');
            $table->dropColumn('country_id');
        });
    }

    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->integer('country_id')->nullable()->unsigned();
            $table->foreign('country_id')->references('id')->on('harassmap_incidents_country');
        });
    }
}