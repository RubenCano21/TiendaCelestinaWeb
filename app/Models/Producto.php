<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $primaryKey = 'codigo_producto';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'codigo_producto',
        'nombre',
        'imagen',
        'precio_unitario',
        'stock',
        'categoria_codigo',
        'unidad_codigo',
    ];

    /**
     * Relación con Categoria
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_codigo', 'codigo_categoria');
    }

    /**
     * Relación con UnidadMedida
     */
    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_codigo', 'codigo_medida');
    }
}
