<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class DonationController extends Controller implements HasMiddleware
{
    //

    public static function middleware() {
        return [
            new Middleware("auth:sanctum", except: ["index"])
        ];
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            "food_name" => "string|required",
            "quantity" => "integer|required",
            "location" => "string|required",
            "category" => "string|required"
        ]);

        if(!Gate::allows("insert-donation")) {
            abort(403, "Dont have access to this resource");
        };

        $donation = Donation::create([
            ...$validatedData,
            "user_id" => $request->user()->id
        ]);
        return response()->json([
            "status" => "Success",
            "message" => "Data inserted",
            "data" => $donation
        ]);
    }

    public function show() {

    }

    public function update(Request $request, $id) { }
}
