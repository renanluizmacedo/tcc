<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\Eixo;
use App\Models\Curso;
use App\Models\User;
use App\Models\Tipousuario;
use Illuminate\Support\Facades\Hash;

class ProfessorController extends Controller {
    
    public function index() {
        
        $data = Professor::with(['eixo', 'user'])->orderBy('nome')->get();
        // return json_encode($data);
        return view('professores.index', compact(['data']));   
    }

    public function create() {

        $eixos = Eixo::orderBy('nome')->get();
        $tipos = Tipousuario::all();
        // return json_encode($data);
        return view('professores.create', compact(['eixos', 'tipos']));   
    }

    public function validation(Request $request, $type) {

        if($type == 0) {
            $rules = [
                'nome' => 'required|max:100|min:5',
                'email' => 'required|unique:professors',
                'siape' => 'required|max:7|min:7',
                'eixo' => 'required',
            ];
        }
        else {
            $rules = [
                'nome' => 'required|max:100|min:5',
                'email' => 'required',
                'siape' => 'required|max:7|min:7',
                'eixo' => 'required',
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
        $total = Professor::where('nome', mb_strtoupper($request->nome, 'UTF-8'))
            ->where('siape', $request->siape)
            ->count();

        if($total > 0) {
            $msg = "Professor";
            $link = "professores.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }

        $eixo = Eixo::find($request->eixo);
        $tipo = Tipousuario::find($request->tipo);
        if(isset($eixo) && isset($tipo)) {
            // Cria o usuário
            $user = new User();
            $user->name = mb_strtoupper($request->nome, 'UTF-8');
            $user->password = Hash::make($request->siape);
            $user->email = $request->email;
            $user->usertype()->associate($tipo);
            $user->save();
            // Cria o Professor
            $obj = new Professor();
            $obj->nome = mb_strtoupper($request->nome, 'UTF-8');   
            $obj->email = $request->email;
            $obj->siape = $request->siape;
            $obj->eixo()->associate($eixo);
            $obj->user()->associate($user);
            $obj->save();
            return redirect()->route('professores.index');
        }

        $msg = "Eixo e/ou Tipo Usuário";
        $link = "professores.index";
        return view('erros.id', compact(['msg', 'link']));
    }

    public function show($id) { }

    public function edit($id) {
        
        $eixos = Eixo::orderBy('nome')->get();
        $tipos = Tipousuario::all();
        $data = Professor::with(['eixo', 'user'])->find($id);
        if(isset($data)) {
            return view('professores.edit', compact(['data', 'eixos', 'tipos']));
        }
        else {
            $msg = "Professor";
            $link = "professores.index";
            return view('erros.id', compact(['msg', 'link']));
        }
    }

    public function update(Request $request, $id) {
        
        self::validation($request, 1);

        // Registro já existente
        /*$total = Professor::where('nome', mb_strtoupper($request->nome, 'UTF-8'))
            ->where('siape', $request->siape)
            ->count();

        if($total > 0) {
            $msg = "Professor";
            $link = "professores.index";
            return view('erros.duplicado', compact(['msg', 'link']));
        }*/

        $eixo = Eixo::find($request->eixo);
        $tipo = Tipousuario::find($request->tipo);
        $obj_prof = Professor::find($id);
        if(isset($eixo) && isset($tipo) && isset($obj_prof)) {
            $obj_user = User::find($obj_prof->user_id);

            if(isset($obj_user)) {
                // Atualiza tipo usuário
                $obj_user->name = mb_strtoupper($request->nome, 'UTF-8');
                $obj_user->password = Hash::make($request->siape);
                $obj_user->email = $request->email;
                $obj_user->usertype()->associate($tipo);
                $obj_user->save();    
                // Atualiza o professor
                $obj_prof->nome = mb_strtoupper($request->nome, 'UTF-8');   
                $obj_prof->email = $request->email;
                $obj_prof->siape = $request->siape;
                $obj_prof->eixo()->associate($eixo);
                $obj_prof->user()->associate($obj_user);
                $obj_prof->save();
                return redirect()->route('professores.index');
            }

            $msg = "Usuário";
            $link = "professores.index";
            return view('erros.id', compact(['msg', 'link']));    
        }

        $msg = "Professor e/ou Eixo e/ou Tipo Usuário";
        $link = "professores.index";
        return view('erros.id', compact(['msg', 'link']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}


/* Nível de acesso
    0 - Professor 
    1 - Técnico
    2 - Coordenador (Professor + Coordenador)
    3 - Admin/Diretor (Professor + Diretor)
*/    