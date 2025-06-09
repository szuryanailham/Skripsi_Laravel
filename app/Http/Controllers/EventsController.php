<?php

namespace App\Http\Controllers;

use App\Models\events;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEventRequest;
use Illuminate\Validation\ValidationException;
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
public function update(Request $request, Events $event)
{
    try {
        // Validasi data hanya jika field dikirimkan
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:events,slug,' . $event->id,
            'price' => 'sometimes|numeric',
            'date' => 'sometimes|date',
            'location' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        // Update hanya field yang dikirim
        $event->fill($validated)->save();

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event
        ], 200);

    } catch (ValidationException $e) {
        // Jika validasi gagal
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        // Jika terjadi error lain
        return response()->json([
            'message' => 'Failed to update event',
            'error' => $e->getMessage(),
        ], 500);
    }
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
