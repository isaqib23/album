<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateNotificationsTable.
 */
class CreateNotificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ["friend_request","album_invitation","post_comment"]);
            $table->string("description");
            $table->integer("sender");
            $table->integer("receiver");
            $table->integer("taggable_id")->nullable();
            $table->enum("status",["sent","accepted","rejected"])->default('sent');

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
		Schema::drop('notifications');
	}
}
