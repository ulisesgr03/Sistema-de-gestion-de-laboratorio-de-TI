<?php

namespace App\Http\Controllers;

use App\Models\Software;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SoftwareController extends Controller
{
    /**
     * Lista los títulos de software y licencias institucionales.
     */
    public function index(Request $request): View|JsonResponse
    {
        $busqueda = $request->query('q');

        $query = Software::with('licencias');

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('fabricante', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        $software = $query->orderBy('nombre')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $software,
            ]);
        }

        return redirect()->route('software.index');
    }
}
