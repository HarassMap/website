<?php namespace Harassmap\News\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapNewsPosts extends Migration
{
    public function up()
    {
        Schema::table('harassmap_news_posts', function($table)
        {
            $table->boolean('hide_image')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_news_posts', function($table)
        {
            $table->dropColumn('hide_image');
        });
    }
}