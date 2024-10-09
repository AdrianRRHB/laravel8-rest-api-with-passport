<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Afectaciones;
use App\Models\AgenteEmpresa;
use App\Models\AgenteServicio;
use App\Models\AgenteTipoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgenteServicioController extends Controller
{
    public function getTipoEmpresas(Request $request)
    {
        $arrayTipos = AgenteTipoEmpresa::where(['estado' => "ACTIVO"])->select("*")->get();

        return response()->json([
            "success" => true,
            "arrayTipos" => $arrayTipos,
            "message" => 'se envio correctamente'
        ]);
    }

    public function getServicios(Request $request)
    {
        $search = '%' . $request->search . '%';
        $servicios = AgenteServicio::where([
            ['agente_servicios.servicio', 'like', $search],
            ['agente_servicios.estado', 'ACTIVO'],
        ])
            ->join('agente_empresas as emp', 'emp.id', 'agente_servicios.empresa_id')
            ->select(
                'agente_servicios.id',
                'agente_servicios.servicio',
                'agente_servicios.codigo',
                'agente_servicios.descripcion',
                'agente_servicios.pago',
                'agente_servicios.comision',
                'agente_servicios.staffuser_id',
                'agente_servicios.estado',
                'agente_servicios.created_at',
                'emp.nombre as empresa',
                'emp.id as empresa_id'
            )
            ->orderBy('agente_servicios.id', 'asc')
            ->get();

        return response()->json([
            "success" => true,
            "servicios" => $servicios,
            "message" => 'se envio correctamente'
        ]);
    }

    public function getServiciosByEmpresaId(Request $request)
    {
        $empresaId = $request->empresaId;
        $servicios = AgenteServicio::where(['empresa_id' => $empresaId])->select("*")->get();
        return response()->json([
            'success' => true,
            'servicios' => $servicios,
            'message' => 'datos con exito',
        ]);
    }

    public function saveServicio(Request $request)
    {

        $descripcion = $request->descripcion;
        $servicio = $request->servicio;
        $codigo = $request->codigo;
        $pago = $request->pago;
        $comision = $request->comision;
        $empresa = $request->empresa;
        $idUser = $request->staffuser;

        $datos = new AgenteServicio();
        $datos->staffuser_id = $idUser;
        $datos->servicio = $servicio;
        $datos->descripcion = $descripcion;
        $datos->codigo = $codigo;
        $datos->pago = $pago;
        $datos->comision = $comision;
        $datos->empresa_id = $empresa;
        $datos->estado = "ACTIVO";
        $datos->staffuser_id = $idUser;
        $datos->save();

        return response()->json([
            'success' => true,
            'message' => 'Image almacenada con exito',
        ]);
    }

    public function saveTipoEmpresa(Request $request)
    {
        $descripcion = $request->descripcion;
        $nombre = $request->nombre;

        $datos = new AgenteTipoEmpresa();
        $datos->estado = "ACTIVO";
        $datos->descripcion = $descripcion;
        $datos->nombre = $nombre;
        $datos->save();

        return response()->json([
            'success' => true,
            'message' => 'Image almacenada con exito',
        ]);
    }

    public function deleteServicio(Request $request)
    {
        $datoAfectacion = AgenteServicio::find($request->id);
        $datoAfectacion->estado = 'INACTIVO';
        $datoAfectacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Request successfully'
        ]);
    }

    public function editServicio(Request $request)
    {
        $data = AgenteServicio::find($request->id);

        if ($data) {

            $data->servicio = $request->servicio;
            $data->descripcion = $request->descripcion;
            $data->pago = $request->pago;
            $data->comision = $request->comision;
            $data->empresa_id = $request->empresa;
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Request successfully'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Request error'
        ]);
    }
}
