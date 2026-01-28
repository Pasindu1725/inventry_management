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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('user_id')->constrained('users'); // User who performed the transaction
            $table->string('type'); // e.g., 'addition', 'deduction'
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
