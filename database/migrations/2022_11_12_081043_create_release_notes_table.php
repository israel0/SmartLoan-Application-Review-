<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReleaseNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_notes', function (Blueprint $table) {
            $table->id();
            $table->boolean("is_added")->default(false);
            $table->text("added_note")->nullable();
            $table->boolean("is_changed")->default(false);
            $table->text("changed_note")->nullable();
            $table->boolean("is_fixed")->default(false);
            $table->text("fixed_note")->nullable();
            $table->text("version_number")->nullable();
            $table->integer("posted_by");
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
        Schema::dropIfExists('release_notes');
    }
}
