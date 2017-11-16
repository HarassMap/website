<?php

namespace Harassmap\Comments\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableUpdateHarassmapCommentsTopic extends Migration
{
    public function up()
    {
        Schema::table('harassmap_comments_topic', function ($table) {
            $table->dropForeign(['code']);
        });
    }

    public function down()
    {
        // do nothing we really don't want this foreign key
    }
}