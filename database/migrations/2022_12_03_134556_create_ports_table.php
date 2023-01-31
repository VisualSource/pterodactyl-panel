<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('allocation_id')->unsigned();

            $table->integer('internal_port')->unsigned()->nullable();
            $table->integer('external_port')->unsigned();

            $table->enum('type', ['both', 'tcp', 'udp'])->default('both');
            $table->enum('method', ['upnp', 'pmp'])->default('upnp');
            $table->string('description')->default('Pterodactyl Port')->nullable();
            $table->string('internal_address')->nullable();

            $table->foreign('allocation_id')->references('id')->on('allocations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ports');
    }
};
