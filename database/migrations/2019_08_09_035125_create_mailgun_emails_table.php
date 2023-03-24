<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailgunEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailgun_emails', function (Blueprint $table) {
            
            if(Schema::getColumnType(config('mailgun-webhooks.user_table.name', 'users'), 'id') === 'integer'){
                $colType = 'unsignedInteger';
            }else{
                $colType = 'unsignedBigInteger';
            }

            $table->bigIncrements('id');
            $table->$colType('user_id')->index()->nullable();
            $table->string('uuid');
            $table->string('recipient_domain')->nullable();
            $table->string('recipient_user')->nullable();
            $table->string('msg_to')->nullable();
            $table->string('msg_from')->nullable();
            $table->string('msg_subject')->nullable();
            $table->string('msg_id')->nullable();
            $table->integer('msg_code')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->boolean('attachments')->default(0);
            $table->timestamps();

            $table->foreign('user_id')
                ->references( config('mailgun-webhooks.user_table.identifier_key') )
                ->on( config('mailgun-webhooks.user_table.name') )
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
        Schema::dropIfExists('mailgun_emails');
    }
}
