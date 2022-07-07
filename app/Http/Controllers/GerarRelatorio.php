<?php

namespace App\Http\Controllers;

use Dompdf\Options;
use App\Models\Aluno;
use App\Models\Conceito;
use Illuminate\Support\Facades\App;

class GerarRelatorio extends Controller
{

    public function gerarGrafico()
    {
        $client = new \GuzzleHttp\Client();

        $chartData = [
            "type" => 'pie',
            "data" => [
                "labels" => ['Coluna 1', 'Coluna 2', 'Coluna 3'],
                "datasets" => [
                    [
                        "label" => "Dados",
                        "data" => [100, 60, 20],
                        "backgroundColor" => ['#27ae60', '#f1c40f', '#e74c3c']
                    ],
                ],
            ]
        ];


        $chartData = json_encode($chartData);

        $chartURL = "https://quickchart.io/chart?c=" . urlencode($chartData);

        $res = $client->get($chartURL);
        $content = (string) $res->getBody();

        $chart = 'data:image/png;base64,' . base64_encode($content);

        return '<img src="' . $chart . '" ';
    }
    public function gerarPDF($aluno)
    {

        $HTML = $this->gerarHTML($aluno);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($HTML);

        return $pdf->stream();
    }
    public function gerarHTML($aluno)
    {
        $logo = './imgs/logoIF.png';
        $grafico = $this->gerarGrafico();
        $aluno = $this->buscarAluno($aluno);
        $nota = $this->buscarNota($aluno);

        $html =
            '<style>
            ' . file_get_contents("./css/pdf.css") . '
            </style>
            <div class ="logo">
                <img src="' . $logo . '">   
            </div>
            <div class"info-aluno">

                <h3>Aluno</h3>

                ' . $aluno . '

            </div>
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
                    '.$nota.'
                </tbody>
            </table>
            <div class ="grafico">
                ' . $grafico . '
            </div>
        ';

        return $html;
    }
    public function buscarAluno($id)
    {
        $aluno = Aluno::where('id','=',$id)->get();

        $htmlAluno = '<p>';
        foreach($aluno as $a){
            $htmlAluno .= 
            '
            <p><b>Nome</b>: ' . $a->nome . '</p>
            <p><b>Ano Ingresso</b>: ' . $a->ano_ingresso . '</p>
            <p><b>NAPNE</b>: ' . $a->napne . '</p>
            <p><b>Curso</b>: ' . $a->curso->nome;
                
        }
        $htmlAluno .= '</p>';

        return $htmlAluno;

    }
    public function buscarNota($id)
    {                

        $notas = Conceito::with(['disciplina'],['professor'],['aluno'])->get();

        $htmlNotas = "<tr>";
        foreach($notas as $nota){
            $htmlNotas = 
            '<td>'.$nota->conceito.'</td>
            <td>'.$nota->disciplina->nome.'</td>
            <td>'.$nota->professor->nome.'</td>
            <td>'.$nota->bimestre.'</td>
            <td>'.$nota->ano.'</td>';
        }
        $htmlNotas .= "</tr>";

        return $htmlNotas;
    }
}
