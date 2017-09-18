<?php

namespace Harassmap\Incidents\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class BuilderTableUpdateHarassmapIncidentsRole extends Migration
{
    public function up()
    {
        Schema::table('harassmap_incidents_role', function ($table) {
            $table->string('name', 100)->change();
            $table->integer('domain_id')->unsigned()->nullable();
            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }

    public function down()
    {
        Schema::table('harassmap_incidents_role', function ($table) {
            $table->string('name', 255)->change();
            $table->dropForeign('harassmap_incidents_role_domain_id_foreign');
            $table->dropColumn('domain_id');
        });
    }
}
