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
        $data = Docencia::all();

        // return json_encode($data);
        return view('docencias.index', compact(['data', 'profs', 'disciplinas', 'cursos']));   
    }

    public function create(Request $request) {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
