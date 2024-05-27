<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id", "issue_title", "issue_description",
        "file_attachment_1", "file_attachment_2", "status",
        "dev_notes", "is_resolved"
    ];

    /**
     * Get the user that owns the SupportTicket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issue_owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
