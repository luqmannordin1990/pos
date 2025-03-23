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
        Schema::dropIfExists('recurring_invoices');



        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // Customer reference
            $table->date('start_date'); // Date when recurrence starts
            $table->date('end_date')->nullable(); // End date (if applicable)
            $table->string('invoice_number')->unique(); // Unique recurring invoice number
            $table->enum('frequency', ['weekly', 'monthly', 'yearly', 'custom'])->default('weekly'); // Recurrence frequency
            $table->string('custom_cron')->nullable(); // Custom cron expression (if applicable)
            $table->integer('limit')->nullable(); // Number of invoices to generate (null = unlimited)
            $table->enum('status', ['active', 'on_hold', 'completed'])->default('active'); // Invoice status
            $table->text('notes')->nullable(); // Additional notes
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
        Schema::dropIfExists('recurring_invoices');
    }
};
