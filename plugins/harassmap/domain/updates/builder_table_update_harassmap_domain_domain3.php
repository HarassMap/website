<?php namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapDomainDomain3 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->boolean('incident')->default(1);
            $table->boolean('intervention')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_domain_domain', function($table)
        {
            $table->dropColumn('incident');
            $table->dropColumn('intervention');
        });
    }
}