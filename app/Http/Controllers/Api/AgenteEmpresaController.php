<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgenteEmpresa;
use App\Models\AgenteTipoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgenteEmpresaController extends Controller
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

    public function getEmpresas(Request $request)
    {
        $search = '%' . $request->search . '%';
        $empresas = AgenteEmpresa::where([
            ['agente_empresas.nombre', 'like', $search],
            ['agente_empresas.estado', 'ACTIVO'],
        ])
            ->join('agente_tipo_empresa as ate', 'ate.id', 'agente_empresas.tipo_entidad')
            ->select(
                'agente_empresas.id',
                'agente_empresas.nombre',
                'agente_empresas.ruc',
                'agente_empresas.direccion',
                'agente_empresas.telefono',
                'agente_empresas.tipo_entidad',
                'agente_empresas.descripcion',
                'agente_empresas.logo',
                'agente_empresas.staffuser_id',
                'agente_empresas.estado',
                'agente_empresas.created_at',
                'ate.nombre as nombre_tipo_entidad'
            )
            ->orderBy('agente_empresas.id', 'asc')
            ->get();

        $total = count($empresas);
        for ($i = 0; $i < $total; $i++) {
            $pathFile = $empresas[$i]->logo;
            //* local
            $url = url('/') . Storage::url("app/public/agente/logos/" . $pathFile);
            //$url2  = Storage::disk('s3')->url('fairdent/imagenes/fotografias/' . $pathFile);
            //* servidor
            // $url = url('/') . Storage::url("fotografias/" . $pathFile);
            $empresas[$i]->file_url = $url;
            // $radio[$i]->file_url_aws = $url2;
        }

        return response()->json([
            "success" => true,
            "empresas" => $empresas,
            "message" => 'se envio correctamente'
        ]);
    }

    public function saveEmpresa(Request $request)
    {

        $fileImage = $request->file('logo');
        $nameImg = $request->logo_name;
        $descripcion = $request->descripcion;
        $nombre = $request->nombre;
        $ruc = $request->ruc;
        $direccion = $request->direccion;
        $telefono = $request->telefono;
        $tipo_entidad = $request->tipo_entidad;
        $idUser = $request->staffuser;
        if ($request->hasFile('logo')) {

            $uniqueid = uniqid();
            $extension = $fileImage->extension();
            $name = time() . $uniqueid . '.' . $extension;

            // guarda la imagen en servidor AWS AMAZON
            // $pathAWS = Storage::disk('s3')->putFileAs('fairdent/imagenes/fotografias', $fileImage, $name);
            // guarda la imagen en servidor local storage
            // $path = $fileImage->storeAs('public/fotografias', $name); // opcion 1 - por defecto esta en local
            $path = Storage::disk('public')->putFileAs('agente/logos', $fileImage, $name); // opcion 2 - seleccionas el disco

            if ($path) {
                $datos = new AgenteEmpresa();
                $datos->staffuser_id = $idUser;
                $datos->tipo_entidad = $tipo_entidad;
                $datos->descripcion = $descripcion;
                $datos->nombre = $nombre;
                $datos->direccion = $direccion;
                $datos->telefono = $telefono;
                $datos->ruc = $ruc;
                $datos->logo = $name;
                $datos->estado = "ACTIVO";
                $datos->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Image almacenada con exito',
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'empty, no hay archivo que cargar',
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

    public function deleteEmpresa(Request $request)
    {
        $datoAfectacion = AgenteEmpresa::find($request->id);
        $datoAfectacion->estado = 'INACTIVO';
        $datoAfectacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Request successfully'
        ]);
    }

    public function editEmpresa(Request $request)
    {

        $fileImage = $request->file('logo');
        $data = AgenteEmpresa::find($request->id);

        if ($data) {


            if ($request->hasFile('logo')) {

                Storage::disk('public')->delete("agente/logos/" . $data->logo);

                $uniqueid = uniqid();
                $extension = $fileImage->extension();
                $name = time() . $uniqueid . '.' . $extension;

                // guarda la imagen en servidor AWS AMAZON
                // $pathAWS = Storage::disk('s3')->putFileAs('fairdent/imagenes/fotografias', $fileImage, $name);
                // guarda la imagen en servidor local storage
                // $path = $fileImage->storeAs('public/fotografias', $name); // opcion 1 - por defecto esta en local
                $path = Storage::disk('public')->putFileAs('agente/logos', $fileImage, $name); // opcion 2 - seleccionas el disco

                if ($path) {
                    $data->descripcion = $request->descripcion;
                    $data->direccion = $request->direccion;
                    $data->nombre = $request->nombre;
                    $data->ruc = $request->ruc;
                    $data->telefono = $request->telefono;
                    $data->tipo_entidad = $request->tipo_entidad;
                    $data->logo = $name;
                    $data->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Image almacenada con exito',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Image no almacenada',
                    ]);
                }
            } else {
                $data->descripcion = $request->descripcion;
                $data->direccion = $request->direccion;
                $data->nombre = $request->nombre;
                $data->ruc = $request->ruc;
                $data->telefono = $request->telefono;
                $data->tipo_entidad = $request->tipo_entidad;
                $data->save();

                return response()->json([
                    'success' => true,
                    'editarAfectacion' => $data,
                    'message' => 'Request successfully'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'editarAfectacion' => $data,
            'message' => 'data id no existe'
        ]);
    }
}
