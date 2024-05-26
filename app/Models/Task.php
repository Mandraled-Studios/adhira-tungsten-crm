<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use App\Enums\BillingAt;
use App\Enums\AssessmentYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'assessment_year',
        'status',
        'duedate',
        'assigned_user_id',
        'frequency_override',
        'billing_status',
        'billing_value',
        'billing_company',
        'task_type_id',
        'client_id',
        'default_frequency',
        'completed_by',
        'invoice_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'duedate' => 'datetime',
        'billing_status' => 'boolean',
        'billing_value' => 'decimal:2',
        'task_type_id' => 'integer',
        'client_id' => 'integer',
        'assessment_year' => AssessmentYear::class,
        'billing_at' => BillingAt::class,
    ];

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assigned_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function taskCheckpoints(): HasMany
    {
        return $this->hasMany(TaskCheckpoint::class);
    }

    /**
     * Get the invoice associated with the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
