<?php

namespace Harassmap\Translate\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateMessagesTable extends Migration
{

    public function up()
    {
        Schema::table('rainlab_translate_messages', function($table)
        {
            $table->integer('domain_id')->unsigned()->nullable();
            $table->foreign('domain_id')->references('id')->on('harassmap_incidents_domain');
        });
    }

    public function down()
    {
        Schema::table('rainlab_translate_messages', function($table)
        {
            $table->dropForeign('rainlab_translate_messages_domain_id_foreign');
            $table->dropColumn('domain_id');
        });
    }

}