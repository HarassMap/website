<?php namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapDomainDomain2 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->string('name', 100);
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->dropColumn('name');
        });
    }
}
