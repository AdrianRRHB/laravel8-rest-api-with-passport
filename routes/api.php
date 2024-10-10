<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\AgenteEmpresaController;
use App\Http\Controllers\Api\AgenteServicioController;
use App\Http\Controllers\Api\AgenteComprobanteController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::resource('posts', PostController::class);
        Route::get('me', [AuthController::class, 'me']);
      
    });
});

Route::middleware('auth:api')->prefix('mantenimiento')->group(function () {

    Route::prefix('usuario')->group(function () {
        Route::post('/registrar-usuario', [UserController::class,'saveUsuario']);
        Route::post('/obtener-usuarios', [UserController::class, 'getUsuarios']);
        Route::post('/eliminar-usuario', [UserController::class,'deleteUsuario']);
    });
});

Route::middleware('auth:api')->prefix('agente')->group(function () {
    Route::post('/registrar-empresa', [AgenteEmpresaController::class,'saveEmpresa']);
    Route::post('/registrar-tipo-empresa', [AgenteEmpresaController::class,'saveTipoEmpresa']);
    Route::post('/obtener-empresas', [AgenteEmpresaController::class,'getEmpresas']);
    Route::post('/obtener-tipo-empresa', [AgenteEmpresaController::class,'getTipoEmpresas']);
    Route::post('/eliminar-empresa', [AgenteEmpresaController::class,'deleteEmpresa']);
    Route::post('/editar-empresa', [AgenteEmpresaController::class,'editEmpresa']);

    Route::prefix('servicio')->group(function () {
        Route::post('/registrar-servicio', [AgenteServicioController::class,'saveServicio']);
        Route::post('/obtener-servicios', [AgenteServicioController::class,'getServicios']);
        Route::post('/eliminar-servicio', [AgenteServicioController::class,'deleteServicio']);
        Route::post('/editar-servicio', [AgenteServicioController::class,'editServicio']);
        Route::post('/obtener-servicio-empresa-id', [AgenteServicioController::class,'getServiciosByEmpresaId']);
    });

    Route::prefix('comprobante')->group(function () {
        Route::post('/registrar-comprobante', [AgenteComprobanteController::class,'saveComprobante']);
        Route::post('/obtener-comprobantes', [AgenteComprobanteController::class,'getComprobantes']);
        Route::post('/eliminar-comprobante', [AgenteComprobanteController::class,'deleteComprobante']);
    });
});
