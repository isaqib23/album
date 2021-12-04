<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePostLikesTable.
 */
class CreatePostLikesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('post_likes', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("post_id");
            $table->unsignedBigInteger("user_id");

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
		Schema::drop('post_likes');
	}
}
