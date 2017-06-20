<?php namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapDomainDomain4 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->integer('country_id')->unsigned()->nullable();

            $table->foreign('country_id')->references('id')->on('harassmap_incidents_country');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->dropColumn('country_id');
        });
    }
}