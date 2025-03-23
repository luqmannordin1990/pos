<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('items');


        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Item name (product/service)
            $table->decimal('price', 10, 2); // Item price (10 digits, 2 decimal places)
            $table->string('unit')->nullable(); // Optional unit of measure
            $table->text('description')->nullable(); // Optional item description
            $table->foreignId('team_id')->constrained()->onDelete('cascade')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
