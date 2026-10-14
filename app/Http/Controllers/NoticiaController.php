<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index()
    {
        // Traemos las noticias más recientes primero
        $noticias = Noticia::latest()->get();
        
        return view('noticias', compact('noticias'));
    }
}