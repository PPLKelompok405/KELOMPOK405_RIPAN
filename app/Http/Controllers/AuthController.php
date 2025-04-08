<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
//
    public function register (Request $request) {
        $validatedData = $this->validateRegisterInput($request);

        $userExistOnDatabase = User::where("email", $validatedData["email"])->exists();
        abort_if($userExistOnDatabase, 400, "User exist on database");

        $newUser = User::create($validatedData);
        return response()->json([
            "status" => "success",
            "message" => "User Created",
            "data" => $newUser
        ], 201);
    }

    private function validateRegisterInput(Request $request): array {
        return $request->validate([
            "name" => "string|max:255|required",
            "email" => "email|max:255|required",
            "password" => "required|string|max:255",
            "role" => Rule::enum(UserRole::class)
        ]);
    }
}
