
<?php
// app/Models/CashStatus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashStatus extends Model
{
    protected $fillable = [
        'user_id',
        'initial_balance',
        'status',
    ];
}