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
            $table->integer('layout_id')->unsigned();
            $table->string('code');
            $table->string('subject');
            $table->text('content_html');
            $table->text('content_text');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable(); 

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
            $table->foreign('layout_id')->references('id')->on('harassmap_mail_layout');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_mail_templates');
    }
}