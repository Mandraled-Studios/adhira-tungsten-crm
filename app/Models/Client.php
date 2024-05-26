<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Invoice;
use App\Enums\BillingAt;
use App\Enums\ClientStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_name',
        'firm_type',
        'pan_number',
        'client_code',
        'client_name',
        'aadhar_number',
        'mobile',
        'whatsapp',
        'email',
        'alternate_email',
        'website',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'tan_no',
        'cin_no',
        'gstin',
        'auditor_group_id',
        'billing_at',
        'client_status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'auditor_group_id' => 'integer',
        'user_id' => 'integer',
        'client_status' => 'boolean',
        'billing_at' => BillingAt::class,
    ];

    public function auditorGroup(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the tasks for the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, Task::class);
    }
}
