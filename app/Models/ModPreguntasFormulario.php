<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModPreguntasFormulario extends Model
{
    protected $table = 'r_bpreguntas_formularios';
    use HasFactory;

    protected $primaryKey = 'RBF_id';
    public $incrementing = true;

    public $timestamps = false;
    // const CREATED_AT = 'createdAt';
    // const UPDATED_AT = 'updatedAt';

    // protected $fillable = [ 'FK_ARC_id', 'FK_REC_id' ];
    protected $guarded = [];
}
