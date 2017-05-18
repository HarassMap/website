<?php namespace Harassmap\Domain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapDomainUser extends Migration
{
    public function up()
    {
        Schema::create('harassmap_domain_user', function($table)
        {
            $table->engine = 'InnoDB';
            $table->smallInteger('user_id')->unsigned();
            $table->smallInteger('domain_id')->unsigned();
            $table->primary(['user_id','domain_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_domain_user');
    }
}
