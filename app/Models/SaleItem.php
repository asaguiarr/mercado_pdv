<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    // Relacionamento: pertence a uma venda
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relacionamento: pertence a um produto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
