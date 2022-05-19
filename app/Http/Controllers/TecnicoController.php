<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Tipousuario;
use Illuminate\Support\Facades\Hash;

class TecnicoController extends Controller {
    
    public function index() {

        $data = Tecnico::with(['user'])->orderBy('nome')->get();
        // return json_encode($data);
        return view('tecnicos.index', compact(['data']));   
    }

    public function create() {
        $tipos = Tipousuario::all();
        // return json_encode($data);
        return view('tecnicos.create', compact(['tipos']));      
    }

    public function validation(Request $request, $type) {

        if($type == 0) {
            $rules = [
                'nome' => 'required|max:100|min:5',
                'email' => 'required|unique:tecnicos',
                'siape' => 'required|max:7|min:7',
            ];
        }
        else {
            $rules = [
                'nome' => 'required|max:100|min:5',
                'email' => 'required',
                'siape' => 'required|max:7|min:7',
            ];
        }
        $msgs = [
            "required" => "O preenchimento do campo [:attribute] é obrigatório!",
            "max" => "O campo [:attribute] possui tamanho máximo de [:max] caracteres!",
            "min" => "O campo [:attribute] possui tamanho mínimo de [:min] caracteres!",
            "unique" => "O campo [:attribute] pode ter apenas um único registro!"
        ];

        $request->validate($rules, $msgs);
    }

    public function store(Request $request) {
        
        self::validation($request, 0);

        // Registro já existente
        $total = Tecnico::where('nome', mb_strtoupper($request->nome, 'UTF-8'))
            ->where('siape', $request->siape)
            ->count();

        if($total > 0) {
            $msg = "Tecnico";
            $link = "tecnicos.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $tipo = Tipousuario::find($request->tipo);
        if(isset($tipo)) {
            // Cria o usuário
            $user = new User();
            $user->name = mb_strtoupper($request->nome, 'UTF-8');
            $user->password = Hash::make($request->siape);
            $user->email = mb_strtolower($request->email, 'UTF-8');
            $user->usertype()->associate($tipo);
            $user->save();
            // Cria o Técnico
            $obj = new Tecnico();
            $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
            $obj->email = mb_strtolower($request->email, 'UTF-8');
            $obj->siape = $request->siape;
            $obj->user()->associate($user);
            $obj->save();
            return redirect()->route('tecnicos.index');
        }

        $msg = "Tipo Usuário";
        $link = "tecnicos.index";
        return view('erros.id', compact(['msg', 'link']));
    }

    public function show($id) { }

    public function edit($id) {
        
        $tipos = Tipousuario::all();
        $data = Tecnico::with(['user'])->find($id);
        if(isset($data)) {
            return view('tecnicos.edit', compact(['data', 'tipos']));
        }
        else {
            $msg = "Técnico";
            $link = "tecnicos.index";
            return view('erros.id', compact(['msg', 'link']));
        }
    }

    public function update(Request $request, $id) {
        
        $tipo = Tipousuario::find($request->tipo);
        $obj_tec = Tecnico::find($id);
        if(isset($tipo) && isset($obj_tec)) {
            $obj_user = User::find($obj_tec->user_id);

            if(isset($obj_user)) {
                // Atualiza tipo usuário
                $obj_user->name = mb_strtoupper($request->nome, 'UTF-8');
                $obj_user->password = Hash::make($request->siape);
                $obj_user->email = mb_strtolower($request->email, 'UTF-8');
                $obj_user->usertype()->associate($tipo);
                $obj_user->save();    
                // Atualiza o técnico
                $obj_tec->nome = mb_strtoupper($request->nome, 'UTF-8');   
                $obj_tec->email = mb_strtolower($request->email, 'UTF-8');
                $obj_tec->siape = $request->siape;
                $obj_tec->user()->associate($obj_user);
                $obj_tec->save();
                return redirect()->route('tecnicos.index');
            }

            $msg = "Usuário";
            $link = "tecnicos.index";
            return view('erros.id', compact(['msg', 'link']));    
        }

        $msg = "Técnico e/ou Tipo Usuário";
        $link = "tecnicos.index";
        return view('erros.id', compact(['msg', 'link']));
    }

    public function destroy($id) { }
}
