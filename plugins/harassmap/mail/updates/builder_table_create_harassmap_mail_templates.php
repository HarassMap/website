<?php namespace Harassmap\Mail\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapMailTemplates extends Migration
{
    public function up()
    {
        Schema::create('harassmap_mail_templates', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->string('code');
            $table->string('subject');
            $table->text('content_html');
            $table->text('content_text');
            $table->integer('layout_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_mail_templates');
    }
}
