<?php

namespace Harassmap\Contact\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateSmallContactFormMessages extends Migration
{
    public function up()
    {
        if (Schema::hasTable('janvince_smallcontactform_messages')) {
            Schema::table('janvince_smallcontactform_messages', function ($table) {
                $table->integer('domain_id')->unsigned();
                $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('janvince_smallcontactform_messages')) {
            Schema::table('janvince_smallcontactform_messages', function ($table) {
                $table->dropForeign('janvince_smallcontactform_messages_domain_id_foreign');
                $table->dropColumn('domain_id');
            });
        }
    }
}