<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{

    public function __construct()
    {   
        $this->middleware('auth');
    }

    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request)
    {
        // Modificar el request
        $request->request->add(['username' => Str::slug($request->username)]);
        
        $this->validate($request, [
            'username' => ['required', 'unique:users,username,'.auth()->user()->id, 'min:3', 'max:20', 'not_in:twitter,editar-perfil'],
        ]);

        if($request->imagen){
            $imagen = $request->file('imagen');

            // Nombre unico a cada imagen que se sube
            $nombreImagen = Str::uuid() . "." . $imagen->extension();
            
            // Imagen que se va a subir al servidor
            $imagenServidor = Image::make($imagen);
            // Setear tamaño de imagen a subir
            $imagenServidor->fit(1000, 1000);
            // Mover la imagen al servidor (Crea la carpeta mas el nombre la imagen)
            $imagenPath = public_path('perfiles') . '/' . $nombreImagen;
            // Gaurdar la imagen en la ruta creada
            $imagenServidor->save($imagenPath);
        }

        // Guardar Cambios
        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? '';
        $usuario->save();

        

        // Redireccionar
        return redirect()->route('posts.index', $usuario->username);
    }
}
