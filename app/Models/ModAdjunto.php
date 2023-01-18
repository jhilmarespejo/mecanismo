<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModAdjunto extends Model
{
    protected $table = 'adjuntos';
    use HasFactory;

    protected $primaryKey = 'ADJ_id';
    public $incrementing = true;
    // const CREATED_AT = 'createdAt';
    // const UPDATED_AT = 'updatedAt';
    public $timestamps = false;

    protected $fillable = ['ADJ_id', 'FK_FRM_id', 'ADJ_titulo', 'ADJ_fecha', 'ADJ_responsables', 'ADJ_entrevistados', 'ADJ_resumen'];
}
