<?php

namespace App\Enums;

enum TicketStatus: string
{
    case PENDING = 'pending';
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';


    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::CLOSED => 'Closed',
        };
    }



    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}