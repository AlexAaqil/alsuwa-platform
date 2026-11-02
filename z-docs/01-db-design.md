# DB Design

## Migrations

```php
Schema::create('leads', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('type')->index(); // individual, business
    $table->string('name')->index();
    $table->string('contact_person')->nullable();
    $table->string('email')->nullable()->index();
    $table->string('phone')->nullable()->index();

    $table->string('source')->nullable()->index();
    $table->string('status')->default('new')->index();
    // new, contacted, qualified, disqualified, converted

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

Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('type')->index(); // individual, business
    $table->string('name')->index();
    $table->string('contact_person')->nullable();
    $table->string('email')->nullable()->index();
    $table->string('phone')->nullable()->index();
    $table->text('address')->nullable();
    $table->text('notes')->nullable();

    $table->foreignId('created_by')
        ->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('updated_by')
        ->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();
});

Schema::create('deals', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('name');
    $table->text('description')->nullable();

    $table->decimal('estimated_value', 12, 2)->nullable();
    $table->char('currency', 3)->default('KES');

    $table->string('stage')->default('qualification')->index();
    // qualification, proposal, negotiation, won, lost

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

Schema::create('activities', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('type')->index();
    // call, meeting, email, note, task

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

Schema::create('product_categories', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();

    $table->boolean('is_active')->default(true)->index();

    $table->foreignId('created_by')
        ->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();
});

Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('name')->index();
    $table->string('sku')->nullable()->unique();
    $table->text('description')->nullable();

    $table->string('type')->index(); // product, service

    $table->string('pricing_type')->default('fixed');// fixed, quotation
    $table->decimal('default_price', 12, 2)->nullable();
    $table->char('currency', 3)->default('KES');

    $table->boolean('is_active')->default(true)->index();

    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();

    $table->timestamps();
});

Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('order_number')->unique();

    $table->string('status')->default('draft')->index();// draft, pending, confirmed, cancelled

    $table->string('source')->default('manual')->index();// manual, pos, website, sales_team, referral

    $table->date('ordered_at')->index();

    $table->char('currency', 3)->default('KES');

    $table->decimal('subtotal', 12, 2)->default(0);
    $table->decimal('discount_amount', 12, 2)->default(0);
    $table->decimal('tax_amount', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2)->default(0);

    $table->text('notes')->nullable();

    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();

    $table->timestamps();

    $table->index(['customer_id', 'status']);
});

Schema::create('order_items', function (Blueprint $table) {
    $table->id();

    $table->string('description');
    $table->decimal('quantity', 12, 3)->default(1);
    $table->decimal('unit_price', 12, 2);

    $table->decimal('discount_amount', 12, 2)->default(0);
    $table->decimal('tax_rate', 5, 2)->default(0);
    $table->decimal('tax_amount', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2);

    $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
    $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

    $table->timestamps();

    $table->index(['order_id', 'product_id']);
});

Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('invoice_number')->unique();

    $table->foreignId('customer_id')
        ->constrained('customers')->restrictOnDelete();

    $table->string('status')->default('draft')->index(); // draft, issued, void

    $table->date('issued_at')->nullable()->index();
    $table->date('due_at')->nullable()->index();

    $table->char('currency', 3)->default('KES');

    $table->decimal('subtotal', 12, 2)->default(0);
    $table->decimal('discount_amount', 12, 2)->default(0);
    $table->decimal('tax_amount', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2)->default(0);

    $table->text('notes')->nullable();

    $table->foreignId('order_id')->nullable()->constrained('orders')->restrictOnDelete();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();

    $table->index(['customer_id', 'status']);
});

Schema::create('invoice_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('invoice_id')
        ->constrained('invoices')->cascadeOnDelete();

    $table->foreignId('order_item_id')
        ->nullable()->constrained('order_items')->nullOnDelete();

    $table->string('description');

    $table->decimal('quantity', 12, 3)->default(1);

    $table->decimal('unit_price', 12, 2);

    $table->decimal('discount_amount', 12, 2)->default(0);

    $table->decimal('tax_rate', 5, 2)->default(0);
    $table->decimal('tax_amount', 12, 2)->default(0);

    $table->decimal('total_amount', 12, 2);

    $table->timestamps();
});

Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('payment_number')->unique();

    $table->foreignId('customer_id')
        ->constrained('customers')->restrictOnDelete();

    $table->decimal('amount', 12, 2);

    $table->char('currency', 3)->default('KES');

    $table->string('method')->index();
    // mpesa, bank_transfer, cash, card, other

    $table->string('reference')->nullable()->index();

    $table->timestamp('received_at')->index();

    $table->string('status')->default('completed')->index();
    // completed, void

    $table->text('notes')->nullable();

    $table->foreignId('created_by')
        ->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();

    $table->index(['customer_id', 'received_at']);
});

Schema::create('invoice_payments', function (Blueprint $table) {
    $table->id();

    $table->foreignId('invoice_id')
        ->constrained('invoices')->restrictOnDelete();

    $table->foreignId('payment_id')
        ->constrained('payments')->restrictOnDelete();

    $table->decimal('amount', 12, 2);

    $table->timestamps();

    $table->unique(['invoice_id', 'payment_id']);
});

Schema::create('expense_categories', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('name')->unique();
    $table->text('description')->nullable();

    $table->boolean('is_active')->default(true)->index();

    $table->foreignId('created_by')
        ->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();
});

Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('expense_number')->unique();

    $table->foreignId('category_id')
        ->constrained('expense_categories')->restrictOnDelete();

    $table->foreignId('order_id')
        ->nullable()->constrained('orders')->nullOnDelete();

    $table->string('description');

    $table->string('payee')->nullable();

    $table->decimal('amount', 12, 2);

    $table->char('currency', 3)->default('KES');

    $table->date('incurred_at')->index();

    $table->date('due_at')->nullable()->index();

    $table->text('notes')->nullable();

    $table->foreignId('created_by')
        ->nullable()->constrained('users')->nullOnDelete();

    $table->foreignId('updated_by')
        ->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();

    $table->index(['category_id', 'incurred_at']);
    $table->index(['order_id', 'incurred_at']);
});

Schema::create('expense_payments', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->foreignId('expense_id')
        ->constrained('expenses')->restrictOnDelete();

    $table->decimal('amount', 12, 2);

    $table->string('method')->index();
    // mpesa, bank_transfer, cash, card, other

    $table->string('reference')->nullable()->index();

    $table->timestamp('paid_at')->index();

    $table->text('notes')->nullable();

    $table->foreignId('created_by')
        ->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();
});
```

## Models

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasUuids;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected $fillable = [
        'type',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}



<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasUuids;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_QUALIFIED = 'qualified';
    public const STATUS_DISQUALIFIED = 'disqualified';
    public const STATUS_CONVERTED = 'converted';

    protected $fillable = [
        'type',
        'name',
        'contact_person',
        'email',
        'phone',
        'source',
        'status',
        'notes',
        'follow_up_at',
        'converted_at',
        'assigned_to',
        'customer_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}



<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    use HasUuids;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public const STAGE_QUALIFICATION = 'qualification';
    public const STAGE_PROPOSAL = 'proposal';
    public const STAGE_NEGOTIATION = 'negotiation';
    public const STAGE_WON = 'won';
    public const STAGE_LOST = 'lost';

    protected $fillable = [
        'name',
        'description',
        'estimated_value',
        'currency',
        'stage',
        'expected_close_date',
        'won_at',
        'lost_reason',
        'lead_id',
        'customer_id',
        'assigned_to',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'expected_close_date' => 'date',
            'won_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}



<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasUuids;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public const TYPE_CALL = 'call';
    public const TYPE_MEETING = 'meeting';
    public const TYPE_EMAIL = 'email';
    public const TYPE_NOTE = 'note';
    public const TYPE_TASK = 'task';

    protected $fillable = [
        'type',
        'subject',
        'description',
        'due_at',
        'completed_at',
        'lead_id',
        'customer_id',
        'deal_id',
        'assigned_to',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}



<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedDeals(): HasMany
    {
        return $this->hasMany(Deal::class, 'assigned_to');
    }

    public function assignedActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'assigned_to');
    }
}
```
