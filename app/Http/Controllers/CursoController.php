<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eixo;
use App\Models\Curso;

class CursoController extends Controller {
    
    public function index() {

        $data = Curso::with(['eixo'])->orderBy('nome')->get();
        // return json_encode($data);
        return view('cursos.index', compact(['data']));   
    }

    public function create() {

        $eixos = Eixo::orderBy('nome')->get();
        return view('cursos.create', compact(['eixos']));   
    }

    public function validation(Request $request) {

        $rules = [
            'nome' => 'required|max:100|min:5',
            'sigla' => 'required|max:7|min:2',
            'tempo' => 'required',
            'eixo' => 'required'
        ];
        $msgs = [
            "required" => "O preenchimento do campo [:attribute] é obrigatório!",
            "max" => "O campo [:attribute] possui tamanho máximo de [:max] caracteres!",
            "min" => "O campo [:attribute] possui tamanho mínimo de [:min] caracteres!",
        ];

        $request->validate($rules, $msgs);
    }

    public function store(Request $request) {

        self::validation($request);

        // Registro já existente
        $total = Curso::where('nome', mb_strtoupper($request->nome, 'UTF-8'))
            ->where('sigla', mb_strtoupper($request->sigla, 'UTF-8'))
            ->where('tempo', $request->tempo)
            ->count();
        if($total > 0) {
            $msg = "Curso";
            $link = "cursos.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $eixo = Eixo::find($request->eixo);
        if(isset($eixo)) {
            $obj = new Curso();
            $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
            $obj->sigla = mb_strtoupper($request->sigla, 'UTF-8');
            $obj->tempo = $request->tempo;
            $obj->eixo()->associate($eixo);
            $obj->save();
            return redirect()->route('cursos.index');
        }

        $msg = "Eixo / Área";
        $link = "cursos.index";
        return view('erros.id', compact(['msg', 'link']));
    }

    public function show($id) { }

    public function edit($id) {
        
        $eixos = Eixo::orderBy('nome')->get();
        $data = Curso::find($id);
        
        if(isset($data)) {
            return view('cursos.edit', compact(['data', 'eixos']));
        }
        else {
            $msg = "Curso";
            $link = "curso.index";
            return view('erros.id', compact(['msg', 'link']));
        }
    }

    public function update(Request $request, $id) {

        self::validation($request);

        // Registro já existente
        $total = Curso::where('nome', mb_strtoupper($request->nome, 'UTF-8'))
            ->where('sigla', mb_strtoupper($request->sigla, 'UTF-8'))
            ->where('tempo', $request->tempo)
            ->count();
        if($total > 0) {
            $msg = "Curso";
            $link = "cursos.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $eixo = Eixo::find($request->eixo);
        $obj = Curso::find($id);
        if(isset($eixo) && isset($obj)) {
            $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
            $obj->sigla = mb_strtoupper($request->sigla, 'UTF-8');
            $obj->tempo = $request->tempo;
            $obj->eixo()->associate($eixo);
            $obj->save();
            return redirect()->route('cursos.index');
        }

        $msg = "Curso ou Eixo/Área";
        $link = "cursos.index";
        return view('erros.id', compact(['msg', 'link']));


    }

    public function destroy($id) {
        
        $obj = Curso::find($id);
        
        if(isset($obj)) {
            $obj->delete();
        }
        else {
            $msg = "Curso";
            $link = "cursos.index";
            return view('erros.id', compact(['msg', 'link']));
        }
        
        return redirect()->route('cursos.index');
    }
}
