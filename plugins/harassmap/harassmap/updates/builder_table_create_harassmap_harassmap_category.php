<?php namespace Harassmap\Harassmap\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHarassmapHarassmapCategory extends Migration
{
    public function up()
    {
        Schema::create('harassmap_harassmap_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('color', 20);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('harassmap_harassmap_category');
    }
}
