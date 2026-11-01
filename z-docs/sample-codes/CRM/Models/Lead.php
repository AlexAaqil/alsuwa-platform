<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Support\Concerns\HasUuid;
use Modules\CRM\Enums\CustomerType;
use Modules\CRM\Enums\LeadStatus;

class Lead extends Model
{
    use HasUuid;

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
            'type' => CustomerType::class,
            'status' => LeadStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
