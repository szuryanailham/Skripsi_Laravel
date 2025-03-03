<?php

namespace App\Http\Controllers;

use App\Models\events;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {  return response()->json([
        'status' => 'success',
    ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(events $events)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, events $events)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(events $events)
    {
        //
    }
}
