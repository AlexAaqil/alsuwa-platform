<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            $table->string('type')->index(); // individual, business
            $table->string('name')->index();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('source')->nullable()->index();

            $table->string('status')->default('new')->index();
            $table->text('notes')->nullable();
            $table->timestamp('follow_up_at')->nullable()->index();
            $table->timestamp('converted_at')->nullable();

            $table->foreignId('assigned_to')
                ->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('customer_id')
                ->nullable()->constrained('customers')->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
