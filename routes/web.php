<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RelatorioCursoController;
use App\Http\Controllers\RelatorioTurmaController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () { return view('index'); })->name('index');

Route::get('/gerarRelatorio/{aluno}', [GerarRelatorio::class, 'gerarPDF'])->name('relatorio');

Route::get('/gerarRelatorio/{aluno}', [GerarRelatorio::class, 'gerarPDF'])->name('relatorio');

Route::get('/relatorioCurso', [RelatorioCursoController::class,'curso'])->name('relatorioCurso');
Route::get('/relatorioTurma', [RelatorioTurmaController::class,'turma'])->name('relatorioTurma');


Route::get('/gerarRelatorioCurso/{id}', [RelatorioCursoController::class,'gerarPDF'])->name('gerarRelatorioCurso');

Route::resource('/eixos', 'EixoController');
Route::resource('/cursos', 'CursoController');
Route::resource('/areas', 'AreaController');
Route::resource('/disciplinas', 'DisciplinaController');
Route::resource('/professores', 'ProfessorController');
Route::resource('/docencias', 'DocenciaController');
Route::resource('/tecnicos', 'TecnicoController');
