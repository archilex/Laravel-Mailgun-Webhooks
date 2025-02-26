<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailgunEmailTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailgun_email_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_id')->index();
            $table->unsignedBigInteger('tag_id')->index();
            $table->timestamps();

            $table->foreign('email_id')
                ->references('id')
                ->on('mailgun_emails')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('mailgun_tags')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailgun_email_tags');
    }
}
