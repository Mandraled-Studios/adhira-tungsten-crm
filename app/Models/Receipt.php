<?php

namespace App\Models;

use App\Enums\PaymentMethods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receipt_number',
        'payment_date',
        'paid_in_full',
        'amount_paid',
        'balance',
        'payment_method',
        'refunded',
        'invoice_id',
        'auditor_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'payment_date' => 'date',
        'paid_in_full' => 'boolean',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'refunded' => 'boolean',
        'invoice_id' => 'integer',
        'auditor_id' => 'integer',
        'payment_method' => PaymentMethods::class,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
