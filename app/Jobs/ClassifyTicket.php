<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\TicketClassifier;

class ClassifyTicket implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Ticket $ticket)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $classifier = new TicketClassifier();
        $response = $classifier->classify($this->ticket);
        if ($response['status'] == 'success') {
            if($this->ticket->is_category_manual) { 
                $this->ticket->update([
                    'confidence' => $response['confidence'],
                    'explanation' => $response['explanation'],
                ]);
            } else {
                $this->ticket->update([
                    'category' => $response['category'],
                    'confidence' => $response['confidence'],
                    'explanation' => $response['explanation'],
                ]);
                $this->ticket->refresh();
            }
        }
    }
}
