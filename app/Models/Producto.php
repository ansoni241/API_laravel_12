<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'cantidad_disponible', 'categoria_id', 'marca_id'
    ];

    /**
     * Un Producto pertenece a una única Categoría.
     *
     * @var array
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Un Producto pertenece a una única Marca.
     *
     * @var array
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Un Producto puede pertenecer a varias instancias de Compra y viceversa.
     *
     * @var array
     */
    public function compras()
    {
        return $this->belongsToMany(Compra::class)->withPivot('precio', 'cantidad', 'subtotal')->withTimestamps();
    }
}
