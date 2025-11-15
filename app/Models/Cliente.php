<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $primaryKey = 'codigo_cliente';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'codigo_cliente',
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'domicilio',
    ];
}
