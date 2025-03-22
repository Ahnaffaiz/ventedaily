<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_id')->constrained('costs')->onDelete('cascade');
            $table->dateTime('date');
            $table->longText('desc')->nullable();
            $table->integer('amount');
            $table->integer('qty');
            $table->string('uom', 25);
            $table->integer('total_amount');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
