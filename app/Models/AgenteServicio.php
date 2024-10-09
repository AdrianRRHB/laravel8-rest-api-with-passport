<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenteServicio extends Model
{
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'agente_servicios';
}
