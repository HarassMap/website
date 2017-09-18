<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapIncidentsCategory extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_category', function($table)
        {
            $table->integer('domain_id')->unsigned();
            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_incidents_category', function($table)
        {
            $table->dropForeign('harassmap_incidents_category_domain_id_foreign');
            $table->dropColumn('domain_id');
        });
    }
}
