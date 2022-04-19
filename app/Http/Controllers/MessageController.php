<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $messages = Message::with('user')->latest()->paginate(20);
            $messages->getCollection()->transform(function ($message) {
                return [
                    'userId'  => $message->user_id,
                    'message' => $message->message,
                    'name'    => $message->user->name,
                ];
            });
            return $messages;
        }

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string'],
        ]);

        Message::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($request->user(), $request->message));

        return response()->json([
            'success' => true,
        ]);
    }
}
