<?php

namespace App\Http\Controllers;

use App\Models\MovimientoPuntos;
use App\Models\Recompensa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $movimientos = MovimientoPuntos::with('pedido')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $recompensas = Recompensa::where('activa', true)->get();

        return view('pages.mis-puntos', compact('user', 'movimientos', 'recompensas'));
    }

    public function canjear(int $id): RedirectResponse
    {
        $recompensa = Recompensa::where('activa', true)->findOrFail($id);
        $user = Auth::user();

        if ($user->puntos_disponibles < $recompensa->puntos_requeridos) {
            return back()->with('error', "No tienes suficientes puntos para canjear '{$recompensa->nombre}'. Necesitas {$recompensa->puntos_requeridos} puntos.");
        }

        DB::transaction(function () use ($user, $recompensa) {
            $user->puntos_disponibles -= $recompensa->puntos_requeridos;
            $user->save();

            MovimientoPuntos::create([
                'user_id' => $user->id,
                'pedido_id' => null,
                'tipo' => 'canjeado',
                'puntos' => -$recompensa->puntos_requeridos,
                'estado' => 'acreditado',
                'motivo' => "Canje de recompensa: {$recompensa->nombre}",
            ]);
        });

        return back()->with('success', "¡Felicitaciones! Has canjeado '{$recompensa->nombre}'. Te hemos generado tu código promocional exclusivo.");
    }
}
