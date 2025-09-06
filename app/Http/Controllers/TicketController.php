<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Resources\TicketResources;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $category = $request->category;
        $query = Ticket::query();
        $query->when($search, function ($query) use ($search) {
            $query->where('subject', 'like', '%' . $search . '%')
            ->orWhere('body', 'like', '%' . $search . '%')
            ->orWhere('note', 'like', '%' . $search . '%');
        });
        $query->when($status && $status != 'all', function ($query) use ($status) {
            $query->where('status', $status);
        });
        $query->when($category && $category != 'all', function ($query) use ($category) {
            $query->where('category', 'like', '%' . $category . '%');
        });
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);

        return [
            'tickets' =>  TicketResources::collection($tickets), 
            'categories' => Ticket::select('category as value', 'category as label')->distinct()->get(),
            'statuses' => TicketStatus::toArray(),
            'total' => $tickets->total(),
            'perPage' => $tickets->perPage(),
            'currentPage' => $tickets->currentPage(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : TicketResources
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => ['required', Rule::in(TicketStatus::cases())],
        ]);

        $ticket = Ticket::create($validated);
        return new TicketResources($ticket);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket) : TicketResources
    {
        return new TicketResources($ticket);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket) : TicketResources
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(TicketStatus::cases())],
            'category' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $ticket->update($validated);
        $ticket->refresh();
        return new TicketResources($ticket);
    }

    public function classify(Request $request, Ticket $ticket)
    {

    }

    public function dashboardStats() : JsonResponse
    {
        // Status counts
        $statusCounts = Ticket::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
    
        // Category counts
        $categoryCounts = Ticket::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');
    
        return response()->json([
            'statuses' => [
                'labels'   => array_keys($statusCounts->toArray()),
                'datasets' => [[
                    'data' => array_values($statusCounts->toArray()),
                ]],
                'total'    => $statusCounts->sum(),
            ],
            'categories' => [
                'labels'   => array_keys($categoryCounts->toArray()),
                'datasets' => [[
                    'data' => array_values($categoryCounts->toArray()),
                ]],
                'total'    => $categoryCounts->sum(),
            ],
            'total' => Ticket::count(),
            'statusesCount' => $statusCounts->toArray(),
        ]);
    }
}
