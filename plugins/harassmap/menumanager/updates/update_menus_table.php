<?php namespace Harassmap\MenuManager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateMenusTable extends Migration
{

    public function up()
    {
        Schema::table('benfreke_menumanager_menus', function($table)
        {
            $table->integer('domain_id')->unsigned()->nullable();
            $table->string('code', 50)->nullable();

            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }

    public function down()
    {
        Schema::table('harassmap_incidents_domain', function($table)
        {
            $table->dropForeign('harassmap_incidents_category_domain_id_foreign');
            $table->dropColumn('domain_id');
            $table->dropColumn('code');
        });
    }

}