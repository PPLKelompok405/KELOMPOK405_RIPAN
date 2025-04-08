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
