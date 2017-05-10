<?php namespace Harassmap\Harassmap\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapHarassmapCategory2 extends Migration
{
    public function up()
    {
        Schema::table('harassmap_harassmap_category', function($table)
        {
            $table->string('title', 255);
            $table->text('description');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_harassmap_category', function($table)
        {
            $table->dropColumn('title');
            $table->dropColumn('description');
        });
    }
}
