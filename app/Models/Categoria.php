<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre','descripcion'
    ];

    /**
     * Una Categoría puede tener muchos productos asociados.
     *
     * @var array
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
