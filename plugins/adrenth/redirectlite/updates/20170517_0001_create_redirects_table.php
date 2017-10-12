<?php

namespace Adrenth\RedirectLite\Updates;

use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * Class CreateRedirectsTable
 *
 * @package Adrenth\RedirectLite\Updates
 */
class CreateRedirectsTable extends Migration
{
    public function up()
    {
        Schema::create('adrenth_redirectlite_redirects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->mediumText('from_url');
            $table->mediumText('to_url')->nullable();
            $table->json('requirements')->nullable();
            $table->char('status_code', 3);
            $table->unsignedInteger('hits')->default(0);
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adrenth_redirectlite_redirects');
    }
}
