<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    public $code;
    public $city_code;
    public $name;
    public $city;
    public $country_code;
    public $latitude;
    public $longitude;
    public $timezone;
}
