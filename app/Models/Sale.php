<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'payment_method',
    ];

    // Relacionamento: uma venda tem vários itens
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Relacionamento: usuário que fez a venda (atendente)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
