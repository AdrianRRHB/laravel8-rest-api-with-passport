<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgenteComprobante;
use App\Models\AgenteEmpresa;
use App\Models\AgenteTipoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgenteComprobanteController extends Controller
{

    public function getComprobantes(Request $request)
    {
        $search = '%' . $request->search . '%';
        $comprobantes = AgenteComprobante::where([
            ['codigo_cliente', 'like', $search],
            ['estado', 'ACTIVO'],
        ])
            ->select(
                'id',
                'numero_operacion',
                'nombre_servicio',
                'monto',
                'comision',
                'total_pagado',
                'fecha_creacion',
                'estado',
                'staffuser_id',
                'estado',
                'codigo_cliente',
                'nombre_cliente',
                'created_at',
            )
            ->orderBy('id', 'asc')
            ->get();


        return response()->json([
            "success" => true,
            "comprobantes" => $comprobantes,
            "message" => 'se envio correctamente'
        ]);
    }

    public function saveComprobante(Request $request)
    {

        if ($request->empresa_id != 0) {

            $datos = new AgenteComprobante();
            $datos->staffuser_id = $request->staffuser;
            $datos->empresa_id = $request->empresa_id;
            $datos->servicio_id = $request->servicio_id;
            $datos->numero_operacion = $request->numero_operacion;
            $datos->nombre_servicio = $request->nombre_servicio;
            $datos->monto = $request->monto;
            $datos->comision = $request->comision;
            $datos->total_pagado = $request->total_pagado;
            $datos->fecha_creacion = $request->fecha_creacion;
            $datos->codigo_cliente = $request->codigo_cliente;
            $datos->nombre_cliente = $request->nombre_cliente;
            $datos->estado = "ACTIVO";
            $datos->save();

            return response()->json([
                'success' => true,
                'message' => 'comprobante almacenada con exito',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'empty, no se encontro el id de la empresa',
        ]);
    }


    public function deleteComprobante(Request $request)
    {
        $datoAfectacion = AgenteComprobante::find($request->id);
        $datoAfectacion->estado = 'INACTIVO';
        $datoAfectacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Request successfully'
        ]);
    }
}
