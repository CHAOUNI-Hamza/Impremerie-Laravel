<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Image;
use App\Models\Category;
use App\Models\User;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'images', 'json', 'category_id', 'user_id', 'slug', 'price', 'qt'
    ];

    protected $casts = [
        'images' => 'array',
        'json' => 'json', // Si 'json' est un champ JSON.
    ];

    public function images()
    {
        return $this->hasMany(Image::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function commande_detail()
    {
        return $this->belongsTo(CommandeDetails::class);
    }
    

}
