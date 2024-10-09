<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenteEmpresa extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'agente_empresas';
}
