<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            $table->string('type')->index();
            $table->string('subject');
            $table->text('description')->nullable();

            $table->timestamp('due_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('lead_id')
                ->nullable()->constrained('leads')->restrictOnDelete();

            $table->foreignId('customer_id')
                ->nullable()->constrained('customers')->restrictOnDelete();

            $table->foreignId('deal_id')
                ->nullable()->constrained('deals')->restrictOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['assigned_to', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
