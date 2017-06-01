<?php namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapDomainContent extends Migration
{
    public function up()
    {
        Schema::table('harassmap_domain_content', function($table)
        {
            $table->string('link', 255);
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_domain_content', function($table)
        {
            $table->dropColumn('link');
        });
    }
}
