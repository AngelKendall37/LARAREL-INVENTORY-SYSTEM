<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'inventory';
    protected $fillable = ['product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
