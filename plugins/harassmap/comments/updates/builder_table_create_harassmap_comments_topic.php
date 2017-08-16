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
            $table->string('id', 20);
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_comments_topic');
    }
}
