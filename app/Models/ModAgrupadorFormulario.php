<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModAgrupadorFormulario extends Model
{
    protected $table = 'agrupador_formularios';
    use HasFactory;

    protected $primaryKey = 'AGF_id';
    public $incrementing = true;

    public $timestamps = false;
    // const CREATED_AT = 'createdAt';
    // const UPDATED_AT = 'updatedAt';
    protected $guarded = [];
}
