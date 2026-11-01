<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('estimated_value', 12, 2)->nullable();
            $table->char('currency', 3)->default('KES');

            $table->string('stage')->default('qualification')->index();
            $table->date('expected_close_date')->nullable()->index();
            $table->timestamp('won_at')->nullable();
            $table->text('lost_reason')->nullable();

            $table->foreignId('lead_id')
                ->nullable()->constrained('leads')->restrictOnDelete();

            $table->foreignId('customer_id')
                ->nullable()->constrained('customers')->restrictOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['stage', 'assigned_to']);
            $table->index(['customer_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
