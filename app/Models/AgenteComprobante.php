<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenteComprobante extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'agente_comprobante_pago';
}
