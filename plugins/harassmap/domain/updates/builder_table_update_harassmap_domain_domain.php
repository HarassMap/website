<?php namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapDomainDomain extends Migration
{
    public function up()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->string('lat', 100);
            $table->string('lng', 100);
            $table->integer('zoom');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
            $table->dropColumn('zoom');
        });
    }
}
