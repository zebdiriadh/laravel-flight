<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    public $airline;
    public $number;
    public $departure_airport;
    public $departure_time;
    public $arrival_airport;
    public $duration;
    public $price;
}
