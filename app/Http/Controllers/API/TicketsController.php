<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\Store;
use App\Models\Tickets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index(){
        $tickets = Tickets::whereUserId(auth()->id())->orderBy('created_at', 'desc')->paginate();
        
        return response()->json([
            'success'=>true,
            'message'=> 'get all my tickets',
            'data' =>$tickets
        ]);
    }
    public function store(Store $request){
        
        $tickets = Tickets::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id()
        ]);


        return response()->json([
            'success' =>true,
            'message' => 'New tickets created',
            'data' => $tickets
        ]);
    }


    public function show(Tickets $ticket): JsonResponse
    {
        if($ticket->user_id !== auth()->id()){
            return response()->json([
                'success'=>false,
                'message'=>'unauthorized',
            ],JsonResponse::HTTP_UNAUTHORIZED);
        }


        return response()->json([
            'success' => true,
            'message' =>'Get detail ticket',
            'data' =>$ticket
        ]);
    }


    public function getQueuePosition(Tickets $ticket): JsonResponse
{
    // Check if the ticket belongs to the authenticated user
    if ($ticket->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access to ticket',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    // Check if the ticket status is 'waiting'
    if ($ticket->status !== 'waiting') {
        return response()->json([
            'success' => false,
            'message' => 'Ticket is not in the waiting queue',
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Calculate queue position
    $position = Tickets::where('status', 'waiting')
        ->where('created_at', '<=', $ticket->created_at)
        ->orderBy('created_at')
        ->count();

    return response()->json([
        'success' => true,
        'message' => 'Ticket queue position retrieved successfully',
        'data' => [
            'ticket_id' => $ticket->id,
            'queue_position' => $position,
        ],
    ]);
}
}
