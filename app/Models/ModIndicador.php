<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModIndicador extends Model
{
    use HasFactory;

    protected $table = 'indicadores';
    protected $primaryKey = 'IND_id';
    public $timestamps = false;

    // Definir las relaciones si es necesario
     // Definir la relación con historial_indicadores
     public function historial()
     {
         return $this->hasMany(ModHistorialIndicador::class, 'FK_IND_id', 'IND_id');
     }
}
