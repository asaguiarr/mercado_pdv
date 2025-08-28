<?php
// app/Models/Customer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'contact',
        'rg',
        'cpf',
        'birthdate',
        'address',
        'photo',
    ];
}