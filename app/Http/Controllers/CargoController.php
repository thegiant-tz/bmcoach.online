<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function store(Request $request)
    {
        $cargo = Cargo::updateOrCreate([
            'route_id' => getRouteInstance($request->from, $request->to)->id,
            'user_id' => authUser()->id,
            'sender_name' => $request->sender_name,
            'sender_phone' => $request->sender_phone,
            'sender_email' => $request->sender_email,
            'receiver_name' => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'item_name' => $request->item_name,
            'item_value' => $request->item_value,
            'weight' => $request->weight,
            'size' => $request->size,
            'amount' => $request->amount,
            'dep_date' => $request->dep_date,
        ]);

        return !is_null($cargo) ? response(['message' => 'success', 'cargo' => $cargo]) : response(['message' => 'failed']);
    }
}
