<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('host');
            $table->string('port')->default('3306');
            $table->string('username');
            $table->string('password');
            $table->string('db_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shards');
    }
}
