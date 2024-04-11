<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Accesorio;

class AccesorioController extends Controller
{
    public function index() {
        $accesorios = Accesorio::all();
        return view('accesorios.index', compact('accesorios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        try {
            $accesorio = new Accesorio();
           
            $file_accesorio = $request->file('imagen');
    
            $extension_accesorio = $file_accesorio->getClientOriginalExtension();
            $namefile_accesorio = Str::random(30) . '.' . $extension_accesorio;
    
            \Storage::disk('public')->put("img/accesorios/" . $namefile_accesorio, \File::get($file_accesorio));
    
            $accesorio->nombre = $request->nombre;
            $accesorio->imagen = $namefile_accesorio;
    
            $accesorio->save();

            $accesorios = Accesorio::all();
    
            return response()->json([
                'success' => true,
                'message' => 'Accesorio creado correctamente',
                'accesorios' => $accesorios,
                'id' => $accesorio->id,
                'imagen' => $namefile_accesorio, 
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el accesorio: ' . $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    public function destroy(Accesorio $accesorio) {
        $imgPath = public_path('img/accesorios/'.$accesorio->imagen);
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
    
        if ($accesorio->delete()) {
            return response()->json(['success' => true, 'message' => 'Accesorio eliminado', 'status' => 200]);
        } else {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el accesorio', 'status' => 500]);
        }
    }
    
    
}

