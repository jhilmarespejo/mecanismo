<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModRespuestaArchivo extends Model
{
    protected $table = 'r_respuestas_archivos';
    use HasFactory;

    protected $primaryKey = 'RPA_id';
    public $incrementing = true;

    // public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $guarded = [];
    // protected $fillable = [ 'FK_ARC_id', 'FK_RES_id' ];
}
