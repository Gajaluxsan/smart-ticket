<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class TicketClassifier
{
    public function classify(Ticket $ticket): array
    {
        $prompt = "Classify this ticket into JSON with keys: category, explanation, confidence.
        Subject: {$ticket->subject}
        Body: {$ticket->body}";

        try {
            
            if(!env('GEMINI_CLASSIFY_ENABLED')) {
                return [
                    'status' => 'success',
                    'category' => Ticket::select('category')->inRandomOrder()->first()->category,
                    'explanation' => 'Gemini classify is not enabled, so we will classify it with dummy data',
                    'confidence' => 0.0,
                ];
            }else{
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                    ->post(env('GEMINI_URL') . '?key=' . env('GEMINI_API_KEY'), [
                        'contents' => [[
                            'parts' => [[
                                'text' => $prompt
                            ]]
                        ]]
                    ]);
                
                $data = $response->json();
                $text = Arr::get($data, 'candidates.0.content.parts.0.text', null);
                if (!$text) {
                    return [
                        'status' => 'error',
                        'category' => Ticket::select('category')->inRandomOrder()->first()->category,
                        'confidence' => 0.5,
                    ];
                }
                $clean = preg_replace('/```(?:json)?|```/', '', $text);
                $json = json_decode(trim($clean), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return [
                        'status' => 'error',
                        'category' => Ticket::select('category')->inRandomOrder()->first()->category,
                        'confidence' => 0.5,
                    ];
                }
                $json['status'] = 'success';
                return $json ?? [
                    'status' => 'error',
                    'category' => Ticket::select('category')->inRandomOrder()->first()->category,
                    'explanation' => 'Could not classify with dummy data',
                    'confidence' => 0.5,
                ];
            }

        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'category' => Ticket::select('category')->inRandomOrder()->first()->category,
                'confidence' => 0.0,
            ];
        }
    }
}
