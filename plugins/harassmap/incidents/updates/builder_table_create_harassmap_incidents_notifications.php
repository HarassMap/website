<?php namespace Harassmap\Incidents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapIncidentsNotifications extends Migration
{
    public function up()
    {
        Schema::create('harassmap_incidents_notifications', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('type', 50);
            $table->string('reference');
            $table->integer('user_id')->unsigned();
            $table->text('content');
            $table->boolean('read')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index(['type', 'reference']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_incidents_notifications');
    }
}