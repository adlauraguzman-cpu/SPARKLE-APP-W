<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resena;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $resenas = Resena::with(['user', 'producto', 'pedido'])->latest()->paginate(15);

        return view('admin.resenas.index', compact('resenas'));
    }

    public function toggleEstado(int $id): RedirectResponse
    {
        $resena = Resena::findOrFail($id);
        $resena->estado = ($resena->estado === 'publicada') ? 'pendiente' : 'publicada';
        $resena->save();

        return back()->with('success', "Estado de la reseña cambiado a «{$resena->estado}».");
    }

    public function destroy(int $id): RedirectResponse
    {
        $resena = Resena::findOrFail($id);
        $resena->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}
