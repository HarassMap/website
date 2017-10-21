<?php namespace Harassmap\Comments\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHarassmapCommentsTopic extends Migration
{
    public function up()
    {
        Schema::table('harassmap_comments_comments', function($table)
        {
            $table->dropForeign('harassmap_comments_comments_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('harassmap_comments_comments', function($table)
        {
            $table->dropForeign('harassmap_comments_comments_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}