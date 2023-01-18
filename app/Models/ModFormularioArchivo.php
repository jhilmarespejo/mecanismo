<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModFormularioArchivo extends Model
{
    protected $table = 'r_formularios_archivos';
    use HasFactory;

    protected $primaryKey = 'RFA_id';
    public $incrementing = true;

    public $timestamps = false;
    // const CREATED_AT = 'createdAt';
    // const UPDATED_AT = 'updatedAt';

    protected $fillable = [ 'FK_FRM_id', 'FK_ARC_id' ];
}
