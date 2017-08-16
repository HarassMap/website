<?php namespace Harassmap\Comments\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapCommentsComments extends Migration
{
    public function up()
    {
        Schema::create('harassmap_comments_comments', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('topic_id', 20);
            $table->integer('user_id')->unsigned();
            $table->text('content');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('topic_id')->references('id')->on('harassmap_comments_topic');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_comments_comments');
    }
}