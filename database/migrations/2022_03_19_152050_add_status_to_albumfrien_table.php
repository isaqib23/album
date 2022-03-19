<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAlbumfrienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('album_friends', function (Blueprint $table) {
            $table->enum("status",["sent","rejected","accepted"])->default("sent");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('album_friends', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
