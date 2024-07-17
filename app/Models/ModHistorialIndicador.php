<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModHistorialIndicador extends Model
{
    use HasFactory;

    protected $table = 'historial_indicadores';
    protected $primaryKey = 'HIN_id';
    public $timestamps = false;

    // Definir las relaciones si es necesario
}
