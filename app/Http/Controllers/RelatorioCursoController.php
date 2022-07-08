<?php

namespace App\Http\Controllers;

use App\Models\Curso;

use App\Models\Conceito;
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
        $nomeCurso = Curso::find($idCurso);
        $data = date('d-m-Y h', time());

        $infoCurso = $this->cursoSelecionado($idCurso);
        $HTML = $this->montarHtml($infoCurso, $nomeCurso, $idCurso);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($HTML);
        return $pdf->stream($nomeCurso->sigla . "-" . $data . '.pdf');
    }

    public function montarHtml($infoCurso, $curso, $idCurso)
    {
        $logo = './imgs/logoIF.png';
        $grafico = $this->gerarGrafico($idCurso);

        $infoTd = $this->htlmTabelaInfoCurso($infoCurso);

        $html =
            '
        <style>
        ' . file_get_contents("./css/pdf.css") . '
        </style>
            <div class ="logo">
            <img src="' . $logo . '">   
        </div>
        <h3>' . $curso->nome . '</h3>
        <h3>Tempo de curso: ' . $curso->tempo . '  anos</h3>
        <table class="table ">
            <thead class = "thead">
                <tr>
                    <th>Aluno</td>
                    <th>Disciplina</td>
                    <th>Conceito</td>
                    <th>Ano</td>
                </tr>
            </thead>
            <tbody class = "tbody">
            ' . $infoTd . '
            </tbody>
        </table>
        <div class ="grafico">
        ' . $grafico . '
        </div>
    ';

        return $html;
    }
    public function htlmTabelaInfoCurso($cursos)
    {


        $tb =  "<tr class= 'tr_table'>";

        foreach ($cursos as $curso) {
            $tb .= "
                <td class='td_table'>" . $curso->aluno->nome . "</td>
                <td class='td_table'>" . $curso->disciplina->nome . "</td>
                <td class='td_table'>" . $curso->conceito . "</td>
                <td class='td_table'>" . $curso->ano . "</td>
                </tr>";
        }

        return $tb;
    }


    public function cursoSelecionado($idCurso)
    {
        $data = Conceito::with(
            [$disciplina = "disciplina" => function ($q) use ($idCurso) {
                return $q->where('disciplinas.curso_id', '=', $idCurso);
            }],
        )->with(
            [$aluno = "aluno" => function ($q) use ($idCurso) {
                $q->where('alunos.curso_id', '=', $idCurso);
                return $q->where('alunos.curso_id', '=', $idCurso);
            }],
        )->get()->where($aluno, '!=', null)->where($disciplina, '!=', null);

        return $data;
    }

    public function gerarGrafico($idCurso)
    {
        $data = $this->cursoSelecionado($idCurso);


        $contConceitos = ["A" => 0, "B" => 0, "C" => 0, "D" => 0];

        function verificarConceitos($data, $contConceitos)
        {
            foreach ($data as $d) {
                if ($d->conceito == 'A') {
                    $contConceitos['A']++;
                } elseif ($d->conceito == 'B') {
                    $contConceitos['B']++;
                } elseif ($d->conceito == 'C') {
                    $contConceitos['C']++;
                } elseif ($d->conceito == 'D') {
                    $contConceitos['D']++;
                }
            };

            $flagA = 0;
            $flagB = 0;
            $flagC = 0;
            $flagD = 0;

            for ($i = 0; $i < 4; $i++) {

                if ($flagA == 0) {
                    if ($contConceitos['A'] == 0) {
                        unset($contConceitos['A']);
                    }
                    $flagA = 1;
                } elseif ($flagB == 0) {
                    if ($contConceitos['B'] == 0) {
                        unset($contConceitos['B']);
                    }
                    $flagB = 1;
                } elseif ($flagC == 0) {
                    if ($contConceitos['C'] == 0) {
                        unset($contConceitos['C']);
                    }
                    $flagC = 1;
                } elseif ($flagD == 0) {
                    if ($contConceitos['D'] == 0) {
                        unset($contConceitos['D']);
                    }
                }
            }
            return $contConceitos;
        }

        $contConceitos = verificarConceitos($data, $contConceitos);

        $client = new \GuzzleHttp\Client();

        $chartData = [
            "type" => 'doughnut',
            "data" => [
                "labels" => [],
                "datasets" => [
                    [
                        "data" => [],
                        "backgroundColor" => ['#4a1309', '#34094a', '#720039', '#2d5b00']
                    ],
                ],
            ],
        ];


        function montarChartData($chartData, $contConceitos)
        {


            $keys = array_keys($contConceitos);

            for ($i = 0; $i < count($keys); $i++) {
                array_push($chartData['data']['labels'], $keys[$i]);
            }

            for ($i = 0; $i < count($contConceitos); $i++) {
                $data[$i] = $contConceitos[$keys[$i]];
            }
            $chartData['data']['datasets'][0]['data'] = $data;

            return $chartData;
        }
        $chartData = montarChartData($chartData, $contConceitos);

        $chartData = json_encode($chartData);

        $chartURL = "https://quickchart.io/chart?c=" . urlencode($chartData);

        $res = $client->get($chartURL);
        $content = (string) $res->getBody();

        $chart = 'data:image/png;base64,' . base64_encode($content);

        return '<img src="' . $chart . '" ';
    }
}
