<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\CommandeDetails;

class Commande extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'statut',
        'user_id',
        'first_name',
        'last_name',
        'country',
        'address',
        'city',
        'telephone',
        'email',
    ];

    /**
     * Get the user that owns the commande.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(CommandeDetails::class);
    }
}
