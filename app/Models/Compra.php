<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtotal', 'total'
    ];

    /**
     * Una Compra puede pertenecer a varias instancias de Producto y viceversa.
     *
     * @var array
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class)->withPivot('precio', 'cantidad', 'subtotal')->withTimestamps();
    }
}
