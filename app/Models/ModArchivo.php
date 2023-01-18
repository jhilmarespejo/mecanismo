<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModArchivo extends Model
{
    protected $table = 'archivos';
    use HasFactory;

    protected $primaryKey = 'ARC_id';
    public $incrementing = true;


    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $guarded = [];
    // protected $fillable = ['ARC_tipoArchivo', 'ARC_NombreOriginal', 'ARC_ruta', 'ARC_extension', 'ARC_tamanyo', 'ARC_tipo', 'ARC_descripcion'];

}
