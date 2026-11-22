<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AromaGuideController extends Controller
{
    public function index(): View
    {
        return view('pages.guia-aromas');
    }

    public function resultado(Request $request): View
    {
        $espacio = $request->input('espacio', 'sala');
        $sensacion = $request->input('sensacion', 'calma');
        $intensidad = $request->input('intensidad', 'media');

        // Lógica de recomendación de aromas de SPARKLE
        if ($sensacion === 'energia' || $espacio === 'trabajo' || $intensidad === 'fresca') {
            $aromaRecomendado = 'HELIOS';
            $perfil = 'Cítrico Solar & Flor de Azahar';
            $descripcion = 'Ideal para despertar los sentidos, llenar tu espacio de luz y vitalidad diurna.';
        } elseif ($sensacion === 'calma' || $espacio === 'estudio' || $intensidad === 'profunda') {
            $aromaRecomendado = 'ALPHA';
            $perfil = 'Maderas Nobles, Ámbar & Especias';
            $descripcion = 'Diseñado para crear una atmósfera cálida, elegante y envolvente al final del día.';
        } else {
            $aromaRecomendado = 'JAZ';
            $perfil = 'Jazmín Nocturno & Té Blanco';
            $descripcion = 'Un respiro floral sutil y sedoso, perfecto para momentos de lectura, desconexión y armonía.';
        }

        $productosAroma = Producto::with('inventario')
            ->where('aroma', $aromaRecomendado)
            ->where('activo', true)
            ->get();

        return view('pages.guia-resultado', compact('aromaRecomendado', 'perfil', 'descripcion', 'productosAroma'));
    }
}
