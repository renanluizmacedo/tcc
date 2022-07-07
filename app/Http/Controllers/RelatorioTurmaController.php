<?php

namespace App\Http\Controllers;
use App\Models\Curso;
use Illuminate\Http\Request;

class RelatorioTurmaController extends Controller
{

    public function turma()
    {
        $data = Curso::with(['eixo'])->orderBy('nome')->get();
        return view('relatorios.relatorio-turmas.index',compact(['data']));   
    }
    public function relatorioTurma($idCurso){

        
    }
    public function associarTurmasCurso($idCurso){

    }
}
