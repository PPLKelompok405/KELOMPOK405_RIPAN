<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    //
    protected $fillable = ["food_name", "quantity", "location", "category", "user_id"];
}
