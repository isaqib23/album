<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateInviteFriendsTable.
 */
class CreateInviteFriendsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invite_friends', function(Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('token', 20)->unique();
            $table->enum('status',["invited","joined"])->default('invited');

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
		Schema::drop('invite_friends');
	}
}
