<?php

use App\Models\Donation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe("Donation Restorant", function () {
    it("it should be able to input donation from resto", function () {
        User::factory()->create([
            "name" => "restotest",
            "email" => "restotest@gmail.com",
            "password" => Hash::make("password"),
            "role" => "penyedia"
        ]);
        $loginResponse  = $this->postJson("/api/auth/login", [
            "email" => "restotest@gmail.com",
            "password" => "password"
        ]);
        $loginResponseJson = $loginResponse->json()["data"];
        $accessToken = $loginResponseJson["accessToken"];

        $data = [
            "food_name" => "name test",
            "quantity" => 10,
            "location" => "Bandung",
            "category" => "makanan"
        ];
        $response = $this->postJson("/api/donations", $data, [
            "Authorization" => "Bearer " . $accessToken,
            ["Accept" => "application/json"]
        ]);
        $responseJson = $response->json();
;
        expect($response->status())->toBe(200);
        expect($responseJson["status"])->toBe("Success");
        expect($responseJson["message"])->toBe("Data inserted");
        expect($responseJson["data"])->not()->toBeNull();
    });

    it("it should be return 403 when regular user trying to create donation", function () {
        User::factory()->create([
            "name" => "usertest",
            "email" => "usertest@gmail.com",
            "password" => Hash::make("password"),
            "role" => "penerima"
        ]);
        $loginResponse  = $this->postJson("/api/auth/login", [
            "email" => "usertest@gmail.com",
            "password" => "password"
        ]);
        $loginResponseJson = $loginResponse->json()["data"];
        $accessToken = $loginResponseJson["accessToken"];

        $data = [
            "food_name" => "name test",
            "quantity" => 10,
            "location" => "Bandung",
            "category" => "makanan"
        ];
        $response = $this->postJson("/api/donations", $data, [
            "Authorization" => "Bearer " . $accessToken
        ]);

        expect($response->status())->toBe(403);
    });

    it("Should return 200 when fetching donations data", function () {
        $response = $this->get("/api/donations", ["Accept" => "application/json"]);
        expect($response->status())->toBe(200);
    }) ;

    it("Should return collection of donations", function () {
        $response = $this->get("/api/donations", ["Accept" => "application/json"]);
        $responseJson = $response->json();

        expect($responseJson["status"])->toBe("Success");
        expect($responseJson["message"])->toContain("retrieved");
        expect($responseJson["data"])->toBeArray();
    });

    describe("Get Donation By Id", function () {
        beforeEach(function ()  {
            $this->resto = User::factory()->create([
                "name" => "restotest",
                "email" => "restotest@gmail.com",
                "password" => Hash::make("password"),
                "role" => "penyedia"
            ]);
            $this->donation =  Donation::factory()->create();
        });

        it("Should return status code 200 if donation exist", function ()  {
            $response = $this->get("/api/donations/". $this->donation->id);
            expect($response->status())->toBe(200);
            expect($response->json()["data"]["user_id"])->toBe($this->resto->id);
        });

        it("Should return 404 when donation not exist", function () {
            $response = $this->get("/api/donations/-100");
            expect($response->status())->toBe(404);
        });
    });


});
