<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $seeder = \App\Models\Setting::where('setting_key', 'payroll_chart_id')->first();
        $seeder->setting_value = 1;
        $seeder->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $seeder = \App\Models\Setting::where('setting_key', 'payroll_chart_id')->first();
        $seeder->setting_value = " ";
        $seeder->save();
    }
}
