<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request){
        // Validate input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'district_id' => 'required|exists:districts,id',
            'police_station_id' => 'required|exists:police_stations,id',
            'post_office_id' => 'required|exists:post_offices,id',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
        ]);

        // Assuming you have an Address model related to the user:
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        Address::create($data);

        return response()->json(['success' => true, 'message' => 'Address saved successfully.']);
    }

    public function destroy(Request $request){

        $address = Address::where('user_id', auth()->id())->findOrFail($request->id);
        $address->delete();

        return response()->json(['message' => 'Deleted']);
    }


}
