<?php namespace Harassmap\Mail\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapMailLayout extends Migration
{
    public function up()
    {
        Schema::create('harassmap_mail_layout', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->string('name', 100);
            $table->text('content_html');
            $table->text('content_text');
            $table->text('content_css');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_mail_layout');
    }
}