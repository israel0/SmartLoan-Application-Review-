<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_tickets', function (Blueprint $table) {
            $table->id();
            $table->string("ticket_id");
            $table->integer("user_id");
            $table->integer("branch_id");
            $table->string("sender_email");
            $table->string("priority");
            $table->text("message");
            $table->string("department");
            $table->string("subject");
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
        Schema::dropIfExists('open_tickets');
    }
}
