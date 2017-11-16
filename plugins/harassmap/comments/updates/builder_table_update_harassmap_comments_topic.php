<?php

namespace Harassmap\Comments\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableUpdateHarassmapCommentsTopic extends Migration
{
    public function up()
    {
        Schema::table('harassmap_comments_topic', function ($table) {
            $table->dropForeign('harassmap_comments_topic_code_foreign');
        });
    }

    public function down()
    {
        Schema::table('harassmap_comments_topic', function ($table) {
            $table->foreign('code')->references('public_id')->on('harassmap_incidents_incident');
        });
    }
}