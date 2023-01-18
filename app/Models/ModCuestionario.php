<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModCuestionario extends Model
{
    protected $table = 'r_bpreguntas_formularios';
    use HasFactory;

    protected $primaryKey = 'RBF_id';
    public $incrementing = true;


    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['FK_FRM_id', 'FK_BCP_id', 'RBF_etiqueta'];

}
