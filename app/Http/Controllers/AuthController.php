<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function login (Request $request) {
        $validatedData = $this->validateLoginInput($request);

        $userExistOnDatabase = User::where("email", $validatedData["email"])->first();
        abort_if(!$userExistOnDatabase, 404, "User not found");
        $isPasswordMatch = Hash::check($validatedData["password"], $userExistOnDatabase->password);
        abort_if(!$isPasswordMatch, 401, "Credential not match");

        $token = $userExistOnDatabase->createToken("access-token");

        return response()->json([
            "status" => "success",
            "message" => "Login success",
            "data" => [
                "accessToken" => $token->plainTextToken
            ]
            ]);

    }

    private function validateLoginInput(Request $request) {
        return $request->validate([
            "email" => "email|required",
            "password" => "required|string|max:255"
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "status" => "success",
            "message" => "Logout successfully"
        ]);
    }
}
