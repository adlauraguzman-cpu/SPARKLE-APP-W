<?php

namespace App\Http\Controllers;

use App\Models\Aroma;
use App\Models\Formula;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $aromas = Aroma::where('activo', true)->get();
        $totalFormulas = Formula::count();
        $totalUsuarios = User::where('rol', 'usuario')->count();

        return view('home', compact('aromas', 'totalFormulas', 'totalUsuarios'));
    }

    public function aromas(): View
    {
        $aromas = Aroma::where('activo', true)->with('reglasAfinidad')->get();

        return view('aromas.index', compact('aromas'));
    }
}
