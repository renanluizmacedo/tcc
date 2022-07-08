<?php

namespace App\Http\Controllers;

use App\Models\Curso;

use App\Models\Conceito;
use Illuminate\Support\Facades\DB;
use Dompdf\Options;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\App;

class RelatorioCursoController extends Controller
{

    public function curso()
    {
        $data = Curso::with(['eixo'])->orderBy('id')->get();
        return view('relatorios.relatorio-cursos.index', compact(['data']));
    }

    public function gerarPDF($idCurso)
    {
        $infoCurso = $this->cursoSelecionado($idCurso);
        //$HTML = $this->montarHtml($infoCurso);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $pdf = App::make('dompdf.wrapper');
        //  $pdf->loadHTML($HTML);

        //return $pdf->stream();
    }
    /*
    public function montarHtml($cursos)
    {
        $infoTd = $this->htlmTabelaInfoCurso($cursos);

        $html =
            '
        <style>
        ' . file_get_contents("./css/pdf.css") . '
        </style>

        <table class="table ">
            <thead class = "thead">
                <tr>
                    <th>Conceito</td>
                    <th>Disciplina</td>
                    <th>Professor</td>
                    <th>Bimestre</td>
                    <th>Ano</td>
                </tr>
            </thead>
            <tbody class = "tbody">
            ' . $infoTd . '
            </tbody>
        </table>

    ';

        return $html;
    }
    public function htlmTabelaInfoCurso($cursos)
    {

        $tb =  "<tr>";

        foreach ($cursos as $curso) {
            $tb = "
            <td>$curso->conceito</td>
            <td>$curso->disciplina</td>
            <td>$curso->professor</td>
            <td>$curso->conceito</td>
            ";
        }

        $tb .= "</tr>";

        return $tb;
    }
    public function cursoSelecionado($idCurso)
    {
       $query = DB::table('conceitos')
            ->join('alunos', 'conceitos.aluno_id', '=', 'id')
            ->join('disciplinas', 'conceitos.disciplina_id', '=', 'disciplinas.id')
            ->join('professors', 'conceitos.professor_id', '=', 'professors.id')
            //->where('disciplinas.curso_id', '=', $idCurso)
            //->where('alunos.curso_id', '=', $idCurso)
            ->get();

            dd($query);
        //return $query;
        
    }*/
    public function cursoSelecionado($idCurso)
    {
        
        $data = Conceito::with(
            [$disciplina = "disciplina" => function ($q) use ($idCurso) {
                  return $q->where('disciplinas.curso_id', '=', $idCurso);

            }],
        )->with(
            [$aluno = "aluno" => function ($q) use ($idCurso) {
                 $q->where('alunos.curso_id', '=', $idCurso);
            }],
        )->get()->where($aluno ,'!=', null)->where($disciplina ,'!=', null);

        return $data;
    }
}
