<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("domains",function(Blueprint $table){
            $table->increments('id');
            $table->string('domain');
            $table->integer('server_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('server_id')->references('id')->on('servers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("domains");
    }
}
