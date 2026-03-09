<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private function assertOwner(Notification $notification)
{
    if ($notification->idUser !== auth()->id()) {
        abort(response()->json(['message' => 'Forbidden'], 403));
    }
}
    public function index()
    {
        $notifications = Notification::where('idUser', auth()->id())->get();
        return response()->json($notifications);    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'idEvent' => 'required|integer|exists:events,idEvent',
        'message' => 'required|string',
        'type' => 'required|string|in:REMINDER,UPDATE,CANCELLATION',
        'seen' => 'sometimes|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validacija nije prošla',
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();
    $data['idUser'] = auth()->id();
    $data['seen'] = $data['seen'] ?? false;

    $notification = Notification::create($data);

    return response()->json($notification, 201);
}

    /**
     * Display the specified resource.
     */
   public function show($id)
{
    $notification = Notification::where('idNotification', $id)->firstOrFail();
    if ($notification->idUser !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }
    return response()->json($notification);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
{
    $notification = Notification::where('idNotification', $id)->firstOrFail();

    if ($notification->idUser !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $validator = Validator::make($request->all(), [
        'idEvent' => 'sometimes|required|integer|exists:events,idEvent',
        'message' => 'sometimes|required|string',
        'type' => 'sometimes|required|string|in:REMINDER,UPDATE,CANCELLATION',
        'seen' => 'sometimes|required|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validacija nije prošla',
            'errors' => $validator->errors()
        ], 422);
    }

    $notification->update($validator->validated());

    return response()->json($notification, 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $notification = Notification::where('idNotification', $id)->firstOrFail();

    if ($notification->idUser !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $notification->delete();

    return response()->json(['message' => 'Notifikacija je obrisana'], 200);
}
  
}
