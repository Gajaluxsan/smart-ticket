<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Resources\TicketResources;
use App\Models\Ticket;
use Illuminate\Http\Request;
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
        
        $tickets = $query->paginate($request->per_page ?? 10);

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => ['required', Rule::in(TicketStatus::toArray())],
        ]);

        $ticket = Ticket::create($validated);
        return new TicketResources($ticket);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return new TicketResources($ticket);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => ['required', Rule::in(TicketStatus::toArray())],
        ]);

        $ticket->update($validated);
        return new TicketResources($ticket);
    }

    public function classify(Request $request, Ticket $ticket)
    {

    }

    public function dashboardStats()
    {
        $tickets = Ticket::count();
        $statusesCount = [
            'total' => $tickets ?? 0,
        ];
        foreach (TicketStatus::cases() as $status) {
            $statusesCount[$status->value] = Ticket::where('status', $status->value)->count() ?? 0;
        }
        $categories = Ticket::select('category')->distinct()->get();
        
        $categoriesCount = [];
        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                $categoriesCount[$category->category] = Ticket::where('category', $category->category)->count() ?? 0;
            }
        }

        return response()->json([
            'statuses' => $statusesCount,
            'categories' => $categoriesCount,
        ]);
    }
}
