<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller {
    
    public function index() {
        
        $data = Area::orderBy('nome')->get();
        return view('areas.index', compact(['data']));
    }

    public function create() {
        return view('areas.create');
    }

    public function validation(Request $request) {
        $rules = [
            'nome' => 'required|max:100|min:5',
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
        $total = Area::where('nome', mb_strtoupper($request->nome, 'UTF-8'))->count();
        if($total > 0) {
            $msg = "Área do Conhecimento";
            $link = "areas.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $obj = new Area();
        $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
        $obj->save();

        return redirect()->route('areas.index');
    }

    public function show($id) { }

    public function edit($id) {
        
        $data = Area::find($id);
        
        if(isset($data)) {
            return view('areas.edit', compact(['data']));
        }
        else {
            $msg = "Áreas do Conhecimento";
            $link = "areas.index";
            return view('erros.id', compact(['msg', 'link']));
        }
    }

    public function update(Request $request, $id) {
        
        self::validation($request);
        
        // Registro já existente
        $total = Area::where('nome', mb_strtoupper($request->nome, 'UTF-8'))->count();
        if($total > 0) {
            $msg = "Área do Conhecimento";
            $link = "areas.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $obj = Area::find($id);
        if(isset($obj)) {
            $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
            $obj->save();
        }
        else {
            $msg = "Área do Conhecimento";
            $link = "necessidades.index";
            return view('erros.id', compact(['msg', 'link']));
        }
        
        return redirect()->route('eixos.index');
    }

    public function destroy($id) {
        
        $obj = Area::find($id);
        
        if(isset($obj)) {
            $obj->delete();
        }
        else {
            $msg = "Área do Conhecimento";
            $link = "areas.index";
            return view('erros.id', compact(['msg', 'link']));
        }
        
        return redirect()->route('areas.index');
    }
}
