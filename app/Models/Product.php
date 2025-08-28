<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
    'code',
    'name',
    'cost_price',
    'profit_margin',
    'sale_price',
    'stock',
];
}