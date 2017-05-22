<?php namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapDomainTip extends Migration
{
    public function up()
    {
        Schema::create('harassmap_domain_tip', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('domain_id')->unsigned();
            $table->string('tip', 255);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_domain_domain');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_domain_tip');
    }
}
