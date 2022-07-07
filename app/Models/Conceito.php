<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conceito extends Model
{
    use HasFactory;

    public function disciplina() {
        return $this->belongsTo('\App\Models\Disciplina');
    }
    public function aluno() {
        return $this->belongsTo('\App\Models\Aluno');
    }

    public function professor() {
        return $this->belongsTo('\App\Models\Professor');
    }
}
