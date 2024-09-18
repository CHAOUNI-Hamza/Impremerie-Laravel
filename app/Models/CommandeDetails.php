<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\Commande;

class CommandeDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'quantity',
        'product_id',
        'price',
        'priceUni',
        'details',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}
