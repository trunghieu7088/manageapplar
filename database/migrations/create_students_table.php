<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('class');
            $table->integer('month_fee');
            $table->date('start_date');
            $table->text('note')->nullable();
            $table->text('status_record');
            $table->timestamps();
        });

        Schema::create('student_fee_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_student');
            $table->foreign('id_student')->references('id')->on('students')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('state');
            $table->integer('fee_value');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
