<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe("Authentication", function () {
    $baseUrl = "/api/auth";
    describe("Register", function () use($baseUrl) {
        it("Should return 422 when payload is not correct", function () use($baseUrl) {
            $registerPayload = [];
            $response = $this->postJson($baseUrl . "/register", $registerPayload);

            expect($response->status())->toBe(422);
        });

        it("Should return 201 when user registered", function () use($baseUrl) {
            $registerPayload = [
                "name" => "mockName",
                "email" => "mockEmail@gmail.com",
                "password" => "password",
                "role" => "penerima"
            ];
            $response = $this->postJson($baseUrl . "/register", $registerPayload);
            $responseData = $response->json()["data"];
            expect($response->status())->toBe(201);
            expect($responseData["name"])->toBe($registerPayload["name"]);
            expect($responseData["email"])->toBe($registerPayload["email"]);
            expect($responseData["role"])->toBe($registerPayload["role"]);
        });

        it("Should return 400 when email is exist on database", function ()use ($baseUrl) {
            $registerPayload = [
                "name" => "mockName",
                "email" => "mockEmail2@gmail.com",
                "password" => "password",
                "role" => "penerima"
            ];
            User::factory()->create($registerPayload);

            $response = $this->postJson($baseUrl . "/register", $registerPayload);
            expect($response->status())->toBe(400);
        });
    });

    describe("login", function () {
        it("Should return 404 if user is not found", function () {
            $loginPayload =[
                "email" => "unknownEmail@gmail.com",
                "password" => "unknown"
            ];
            $response = $this->postJson("/auth/api/login", $loginPayload);
            expect($response->status())->toBe(404);
        });

        it("should be return 422 if payload is not  correct", function () {
            $loginPayload =[
                "email" => "unknownEmail",
                "password" => "unknown"
            ];
            $response = $this->postJson("/api/auth/login", $loginPayload);

            expect($response->status())->toBe(422);
        });

        it("Should response correctly", function () {
            $loginPayload =[
                "email" => "knownEmail@gmail.com",
                "password" => "password"
            ];
            User::factory()->create([
                ...$loginPayload,
                "name" => "mock name",
                "role" => "penerima"
            ]);
            $response = $this->postJson("/api/auth/login", $loginPayload);
            $responseJson = $response->json()["data"];
            expect($response->status())->toBe(200);
            expect($responseJson["accessToken"])->toBeString();
        });

        it("Should return 401 if password is not correct", function () {
            $loginPayload = [
                "email" => "knownEmail@gmail.com",
                "password" => "password"
            ];
            User::factory()->create([
                ...$loginPayload,
                "name" => "mock name",
                "role" => "penerima"
            ]);
            $response = $this->postJson("/api/auth/login", [
                ...$loginPayload,
                "password" => "not-match"
            ]);
            expect($response->status())->toBe(401);
        });
    });

    describe("Logout", function () {
        it("Should return 401 if access token not sended", function () {
            $response = $this->withHeaders(["accept" => "application/json"])->post("/api/auth/logout");

            expect($response->status())->toBe(401);
        });

        it("Should delete current token when user logout", function () {
            $loginPayload = [
                "email" => "knownEmail@gmail.com",
                "password" => "password"
            ];
            $user = User::factory()->create([
                ...$loginPayload,
                "name" => "mock name",
                "role" => "penerima"
            ]);
            $token = $user->createToken("access-token")->plainTextToken;
           $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
            "accept" => "application/json"
           ])->post("/api/auth/logout");

           $tokenExist = $user->tokens()->where("name", "access-token")->first();

           expect($response->status())->toBe(200);
           expect($tokenExist)->toBeNull();
        });

    });
});
