<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;
    //
    protected $fillable = ["food_name", "quantity", "location", "category", "user_id"];
}
