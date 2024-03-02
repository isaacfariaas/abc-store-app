<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $table = 'sales';
    protected $fillable = ['total_value', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function productsSale()
    {
        return $this->hasMany(ProductSale::class, 'sale_id');
    }
}
