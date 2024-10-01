<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'bottlesize',
        'name',
        'price',
        'priceGBP',
        'timestamp',
        'orderamount'
    ];
}
