<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ImagenController extends Controller
{
    //
    public function store(Request $request)
    {
        $imagen = $request->file('file');

        // Nombre unico a cada imagen que se sube
        $nombreImagen = Str::uuid() . "." . $imagen->extension();
        
        // Imagen que se va a subir al servidor
        $imagenServidor = Image::make($imagen);
        // Setear tamaño de imagen a subir
        $imagenServidor->fit(1000, 1000);
        // Mover la imagen al servidor (Crea la carpeta mas el nombre la imagen)
        $imagenPath = public_path('uploads') . '/' . $nombreImagen;
        // Gaurdar la imagen en la ruta creada
        $imagenServidor->save($imagenPath);
        
        return response()->json(['imagen' => $nombreImagen]);
    }
}
