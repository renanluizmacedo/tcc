<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\Disciplina;
use App\Models\Docencia;
use App\Models\Curso;
use App\Models\Ano;

class DocenciaController extends Controller {
    
    public function index() {
        
        $cursos = Curso::orderBy('nome')->get();
        $ano = Ano::where('atual', 1)->first()->ano_letivo;
        $disciplinas = Disciplina::with(['curso'])->where('ano', $ano)
            ->orderBy('curso_id')->orderBy('nome')->get();
        $profs = Professor::orderBy('nome')->get();
        $data = Docencia::where('ano', $ano)->get();

        // return json_encode($data);
        return view('docencias.index', compact(['data', 'profs', 'disciplinas', 'cursos', 'ano']));   
    }

    public function create(Request $request) { }

    public function store(Request $request) { }

    public function link(Request $request) {

        $ano = $request->input('ano');
        $arr = explode(" ", $request->input('ids'));

        foreach($arr as $item) {
            $ids = explode("_", $item);
            $disciplina = Disciplina::find($ids[0]);
            $professor = Professor::find($ids[1]);

            // Verifica se professor e disciplina foram encontrados
            if(isset($disciplina) && isset($professor)) {

                // Verifica se a disciplina já possui professor vinculado - remove vinculo, caso sim
                $vinculo = Docencia::where('disciplina_id', $ids[0])->where('ano', $ano);
                if(isset($vinculo)) { $vinculo->delete(); }

                // Cria novo vínculo discplina+professor
                $docencias = new Docencia();
                $docencias->ano = $ano;
                $docencias->disciplina()->associate($disciplina);
                $docencias->professor()->associate($professor);
                $docencias->save();
            }
        }

        return "($ano)";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
