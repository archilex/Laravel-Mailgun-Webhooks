<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailgunEmailContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailgun_email_content', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_id')->index();
            $table->string('subject')->nullable();
            $table->string('to')->nullable();
            $table->string('content_type')->nullable();
            $table->string('message_id')->nullable();
            $table->text('stripped_text')->nullable();
            $table->longText('stripped_html')->nullable();
            $table->longText('body_html')->nullable();
            $table->mediumText('body_plain')->nullable();
            $table->timestamps();

            $table->foreign('email_id')
                ->references('id')
                ->on('mailgun_emails')
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
        Schema::dropIfExists('mailgun_email_content');
    }
}
