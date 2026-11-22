<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Resena;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'pedido_id' => 'required|exists:pedidos,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|min:10|max:1000',
            'autoriza_publicacion' => 'nullable|boolean',
        ], [
            'calificacion.required' => 'Por favor califica el producto con estrellas.',
            'comentario.required' => 'Escribe tu experiencia con el producto.',
            'comentario.min' => 'El comentario debe tener al menos 10 caracteres.',
        ]);

        $user = Auth::user();

        // Validar que el pedido pertenezca al usuario y esté en estado 'entregado'
        $pedido = Pedido::where('id', $validated['pedido_id'])
            ->where('user_id', $user->id)
            ->where('estado', 'entregado')
            ->first();

        if (! $pedido) {
            return back()->with('error', 'Solo puedes reseñar productos que ya hayan sido entregados.');
        }

        // Validar que no haya reseñado previamente este producto en este pedido
        $existente = Resena::where('user_id', $user->id)
            ->where('producto_id', $validated['producto_id'])
            ->where('pedido_id', $pedido->id)
            ->first();

        if ($existente) {
            return back()->with('error', 'Ya has enviado una reseña para este producto.');
        }

        Resena::create([
            'user_id' => $user->id,
            'producto_id' => $validated['producto_id'],
            'pedido_id' => $pedido->id,
            'calificacion' => $validated['calificacion'],
            'comentario' => $validated['comentario'],
            'autoriza_publicacion' => $request->boolean('autoriza_publicacion', true),
            'estado' => 'publicada',
        ]);

        return back()->with('success', '¡Gracias por tu reseña! Tu opinión ayuda a otros amantes de los aromas a elegir su kit ideal.');
    }
}
