<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;
    protected $table = 'r_bpreguntas_formularios';

    protected $primaryKey = 'RBF_id';
    public $incrementing = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['PRG_id', 'FK_FRM_id', 'FK_BCP_id', 'createdBy', 'createdAt', 'updatedBy', 'updatedAt', 'deletedBy', 'deletedAt'];
}
