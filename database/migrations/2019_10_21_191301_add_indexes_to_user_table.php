<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index([\Illuminate\Support\Facades\DB::raw('name(7)')]);
            $table->index([\Illuminate\Support\Facades\DB::raw('last_name(5)')]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex([\Illuminate\Support\Facades\DB::raw('name(7)')]);
            $table->dropIndex([\Illuminate\Support\Facades\DB::raw('last_name(5)')]);
        });
    }
}
