<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailgunEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailgun_events', function (Blueprint $table) {
            
            // Required for inspecting column type of tables with enum fields in it
            DB::getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            $table->bigIncrements('id');
            $table->unsignedBigInteger('email_id')->index();
            $table->enum('event_type', config('mailgun-webhooks.event_types') )->index();
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
        Schema::dropIfExists('mailgun_events');
    }
}
