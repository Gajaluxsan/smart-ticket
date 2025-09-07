<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class BulkClassifyTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:bulk-classify {--limit=10 : Number of tickets per minute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk classify tickets using AI with rate limiting';

    /**
     * Execute the console command.
     */
    public function handle(TicketClassifier $classifier): void
    {
        $limit = (int) $this->option('limit') ?? 10;
        $this->info("Starting bulk classification (limit {$limit} per minute)...");

        $tickets = Ticket::whereNull('category')->orderBy('created_at', 'asc')->get();
        foreach ($tickets as $ticket) {
            RateLimiter::hit('ticket-classify', 60);
            $available = RateLimiter::remaining('ticket-classify', $limit);

            if ($available <= 0) {
                $this->info("Rate limit reached, sleeping 60 seconds...");
                sleep(60);
                RateLimiter::clear('ticket-classify');
            }

            $result = $classifier->classify($ticket);
            if ($result['status'] == 'success') {
                if($ticket->is_category_manual) {
                    $ticket->update([
                        'confidence' => $result['confidence'] ?? 0.5,
                        'explanation' => $result['explanation'] ?? 'No explanation',
                    ]);
                } else {
                    $ticket->update([
                        'category' => $result['category'] ?? 'Other',
                        'confidence' => $result['confidence'] ?? 0.5,
                        'explanation' => $result['explanation'] ?? 'No explanation',
                    ]);
                }
  
            }
            $this->info("Ticket #{$ticket->id} classified as: {$ticket->category}");
            Log::info('Ticket classified', ['ticket_id' => $ticket->id, 'result' => $result]);
        }

        $this->info('Bulk classification completed!');
    }
}
