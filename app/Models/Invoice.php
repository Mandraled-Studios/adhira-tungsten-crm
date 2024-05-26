<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Client;
use App\Enums\TaxLabel;
use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'duedate',
        'subtotal',
        'tax1',
        'tax2',
        'total',
        'tax1_label',
        'tax2_label',
        'invoice_status',
        'task_id',
        'task_description'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invoice_date' => 'date',
        'invoice_status' => InvoiceStatus::class,
        'duedate' => 'date',
        'subtotal' => 'decimal:2',
        'tax1' => 'decimal:2',
        'tax2' => 'decimal:2',
        'tax1_label' => TaxLabel::class,
        'tax2_label' => TaxLabel::class,
        'total' => 'decimal:2',
        'task_id' => 'integer',
        'hsncode' => 'integer:8',
        'roundoff' => 'decimal:2',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function client(): HasOneThrough
    {
        return $this->hasOneThrough(Client::class, Task::class, 'id', 'id', 'task_id', 'client_id');
    }
}
