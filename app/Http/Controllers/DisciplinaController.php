<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disciplina;
use App\Models\Curso;
use App\Models\Area;
use App\Models\Ano;
use Illuminate\Support\Facades\Log;

class DisciplinaController extends Controller {
    
    public function index() {

        $ano = Ano::where('atual', 1)->first()->ano_letivo;
        $data = Disciplina::with(['curso', 'area'])
            ->where('ano', $ano)->orderBy('curso_id')
            ->orderBy('nome')->get();
        // return json_encode($data);
        return view('disciplinas.index', compact(['data']));   
    }

    public function create() {

        $cursos = Curso::orderBy('nome')->get();
        $areas = Area::orderBy('nome')->get();
        $ano = Ano::where('atual', 1)->first()->ano_letivo;
        return view('disciplinas.create', compact(['cursos', 'areas', 'ano']));   
    }

    public function validation(Request $request) {

        $rules = [
            'nome' => 'required|max:100|min:5',
            'carga' => 'required',
            'ano_letivo' => 'required',
            'area' => 'required',
            'curso' => 'required',
            'periodo' => 'required'
        ];
        $msgs = [
            "required" => "O preenchimento do campo [:attribute] é obrigatório!",
            "max" => "O campo [:attribute] possui tamanho máximo de [:max] caracteres!",
            "min" => "O campo [:attribute] possui tamanho mínimo de [:min] caracteres!",
        ];

        $request->validate($rules, $msgs);
    }

    public function store(Request $request) {

        // Log::debug("[Antes / Validação de Entrada]");
        self::validation($request);

        // Registro já existente
        $total = Disciplina::where('nome', mb_strtoupper($request->nome, 'UTF-8'))
            ->where('ano', $request->ano_letivo)
            ->where('curso_id', $request->curso)
            ->count();
        
        if($total > 0) {
            $msg = "Disciplina";
            $link = "disciplinas.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $curso = Curso::find($request->curso);
        $area = Area::find($request->area);
        if(isset($curso) && isset($area)) {
            $obj = new Disciplina();
            $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
            $obj->ano = $request->ano_letivo;
            $obj->periodo = $request->periodo;
            $obj->carga = $request->carga;
            $obj->curso()->associate($curso);
            $obj->area()->associate($area);
            $obj->save();
            return redirect()->route('disciplinas.index');
        }

        $msg = "Curso e/ou Área do Conhecimento";
        $link = "disciplinas.index";
        return view('erros.id', compact(['msg', 'link']));
    }

    public function show($id) { }

    public function edit($id) {
        
        $cursos = Curso::orderBy('nome')->get();
        $areas = Area::orderBy('nome')->get();
        $data = Disciplina::find($id);
        
        if(isset($data)) {
            return view('disciplinas.edit', compact(['data', 'cursos', 'areas']));
        }
        else {
            $msg = "Disciplina";
            $link = "disciplinas.index";
            return view('erros.id', compact(['msg', 'link']));
        }
    }

    public function update(Request $request, $id) {
        
        self::validation($request);

        // Registro já existente
        $total = Disciplina::where('nome', mb_strtoupper($request->nome, 'UTF-8'))
            ->where('ano', $request->ano_letivo)
            ->where('curso_id', $request->curso)
            ->count();
        
        if($total > 0) {
            $msg = "Disciplina";
            $link = "disciplinas.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $curso = Curso::find($request->curso);
        $area = Area::find($request->area);
        $obj = Disciplina::find($id);

        if(isset($obj) && isset($curso) && isset($area)) {
            $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
            $obj->ano = $request->ano_letivo;
            $obj->periodo = $request->periodo;
            $obj->carga = $request->carga;
            $obj->curso()->associate($curso);
            $obj->area()->associate($area);
            $obj->save();
            return redirect()->route('disciplinas.index');
        }

        $msg = "Disciplina e/ou Curso e/ou Área do Conhecimento";
        $link = "disciplinas.index";
        return view('erros.id', compact(['msg', 'link']));

    }

    public function destroy($id) {

        $obj = Disciplina::find($id);
        
        if(isset($obj)) {
            $obj->delete();
        }
        else {
            $msg = "Disciplina";
            $link = "disciplinas.index";
            return view('erros.id', compact(['msg', 'link']));
        }
        
        return redirect()->route('disciplinas.index');
    }
}
