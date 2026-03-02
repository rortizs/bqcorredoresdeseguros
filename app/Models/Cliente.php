<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'comentario',
    ];
}
