<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailgunVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailgun_variables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_id')->index();
            $table->string('key');
            $table->string('value');
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
        Schema::dropIfExists('mailgun_variables');
    }
}
