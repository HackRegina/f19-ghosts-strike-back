<?php namespace LukeTowers\APSS\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateNeedleReportsTable extends Migration
{
    public function up()
    {
        Schema::create('luketowers_apss_needle_reports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('status')->default('submitted');
            $table->text('description')->nullable();
            $table->text('data')->nullable();
            $table->string('ip_address');
            $table->integer('needles_collected')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('luketowers_apss_needle_reports');
    }
}
