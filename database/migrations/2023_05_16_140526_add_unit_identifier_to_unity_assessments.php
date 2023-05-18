<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdentifierToUnityAssessments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unity_assessments', function (Blueprint $table) {
            $table->string('unit_identifier')->nullable();
            $table->foreign('unit_identifier')->references('identifier')->on('v2_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unity_assessments', function (Blueprint $table) {
            $table->dropColumn('unit_identifier');
        });
    }
}
