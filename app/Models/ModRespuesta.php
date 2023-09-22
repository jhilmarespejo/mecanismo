<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModRespuesta extends Model
{
    protected $table = 'respuestas';
    use HasFactory;

    protected $primaryKey = 'RES_id';
    public $incrementing = true;
    // public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $guarded = [];


    // protected $fillable = ['RES_respuesta', 'FK_RBF_id', 'RES_tipoRespuesta','RES_complementoRespuesta', 'res_complemento'];
}
