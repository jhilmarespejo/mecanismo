<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModRecomendacionArchivo extends Model
{
    protected $table = 'r_recomendaciones_archivos';
    use HasFactory;

    protected $primaryKey = 'RRA_id';
    public $incrementing = true;

    public $timestamps = false;
    // const CREATED_AT = 'createdAt';
    // const UPDATED_AT = 'updatedAt';

    protected $fillable = [ 'FK_ARC_id', 'FK_REC_id' ];
}
