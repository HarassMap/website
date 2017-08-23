<?php namespace Harassmap\User\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateFrontendUser1 extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->integer('domain_id')->unsigned()->nullable();
            $table->foreign('domain_id', 'domain_id_foreign')->references('id')->on('harassmap_incidents_domain');
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropForeign('domain_id_foreign');
            $table->dropColumn('domain_id');
        });
    }
}