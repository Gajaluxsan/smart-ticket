<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasUlids, HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'status',
        'category',
        'note',
        'confidence',
        'explanation',
        'is_category_manual',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'is_category_manual' => 'boolean',
    ];

}
