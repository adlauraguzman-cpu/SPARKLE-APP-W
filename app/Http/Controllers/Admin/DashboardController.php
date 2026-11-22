<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\MovimientoPuntos;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $isColaborador = $user->isColaborador();

        // Si es colaborador (Fase 2), solo ve panel operativo sin información comercial sensible (Punto 1 y 2)
        $totalVentas = $isColaborador ? null : Pedido::where('estado_pago', 'aprobado')->where('estado', '!=', 'cancelado')->sum('total');
        $pedidosNuevos = Pedido::whereIn('estado', ['pagado', 'en_preparacion'])->count();
        $pedidosEnviados = Pedido::where('estado', 'enviado')->count();
        $pedidosEntregados = Pedido::where('estado', 'entregado')->count();

        $productosBajoStock = Inventario::with('producto')
            ->whereColumn('stock_disponible', '<=', 'umbral_minimo')
            ->count();

        $totalClientes = $isColaborador ? null : User::where('rol', 'cliente')->count();
        $puntosAcreditados = $isColaborador ? null : MovimientoPuntos::where('estado', 'acreditado')->where('tipo', 'ganado')->sum('puntos');

        $ultimosPedidos = Pedido::with(['user', 'items.producto'])
            ->latest()
            ->take(6)
            ->get();

        $alertasStock = Inventario::with('producto')
            ->whereColumn('stock_disponible', '<=', 'umbral_minimo')
            ->get();

        $ultimosMovimientos = MovimientoInventario::with('producto')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'isColaborador',
            'totalVentas',
            'pedidosNuevos',
            'pedidosEnviados',
            'pedidosEntregados',
            'productosBajoStock',
            'totalClientes',
            'puntosAcreditados',
            'ultimosPedidos',
            'alertasStock',
            'ultimosMovimientos'
        ));
    }
}
