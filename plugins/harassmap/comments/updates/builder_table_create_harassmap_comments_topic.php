<?php

namespace Harassmap\Comments\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableCreateHarassmapCommentsTopic extends Migration
{
    public function up()
    {
        Schema::create('harassmap_comments_topic', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('code', 20);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('code')->references('public_id')->on('harassmap_incidents_incident');
        });
    }

    public function down()
    {
        Schema::dropIfExists('harassmap_comments_topic');
    }
}