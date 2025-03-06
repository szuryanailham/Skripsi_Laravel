<?php

namespace App\Http\Controllers;

use App\Models\events;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEventRequest;
class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $events = events::all();
            return response()->json([
                'status' => 'success',
                'data' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        try {
            $event = events::create($request->all());

            return response()->json([
                'status' => 'success',
                'data' => $event,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            $event = events::where('slug', $slug)->firstOrFail();
            return response()->json([
                'status' => 'success',
                'data' => $event,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEventRequest $request, Events $event)
{

    $event->update([
        'title' => $request->title,
        'slug' => $request->slug,
        'price' => $request->price,
        'date' => $request->date,
        'location' => $request->location,
        'description' => $request->description,
    ]);


    // Kembalikan respons sukses
    return response()->json([
        'message' => 'Event updated successfully',
        'event' => $event
    ], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Events $event)
    {
        try {
            $event->delete();
            return response()->json([
                'message' => 'Event deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function userAllEvents()
    {
        try {
            $events = events::all();
            return response()->json([
                'status' => 'success',
                'data' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function DetailEvents($slug)
    {
        try {
            $event = events::where('slug', $slug)->firstOrFail();
            return response()->json([
                'status' => 'success',
                'data' => $event,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
