<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenteTipoEmpresa extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'agente_tipo_empresa';
}
