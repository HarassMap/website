<?php namespace Harassmap\Comments\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapCommentsTopic extends Migration
{
    public function up()
    {
        Schema::create('harassmap_comments_topic', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('code', 20);

            $table->foreign('code')->references('public_id')->on('harassmap_incidents_incident');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_comments_topic');
    }
}