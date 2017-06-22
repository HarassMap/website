<?php namespace Harassmap\News\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapNewsPosts extends Migration
{
    public function up()
    {
        Schema::create('harassmap_news_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('domain_id')->unsigned();
            $table->string('title', 100);
            $table->string('slug', 100);
            $table->text('intro');
            $table->text('content');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_news_posts');
    }
}