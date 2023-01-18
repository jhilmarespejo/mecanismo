<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModEstablecimiento extends Model
{
    protected $table = 'establecimientos';
    use HasFactory;

    protected $primaryKey = 'EST_id';
    public $incrementing = true;


    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['EST_nombre', 'EST_direccion', 'EST_normativaInterna', 'EST_telefonoContacto', 'EST_genero', 'EST_grupoGeneracional', 'EST_poblacion', 'EST_superficie', 'EST_superficieConstruida', 'FK_CID_id', 'FK_TES_id', 'FK_NSG_id', 'EST_coberturaMunicipios'];


}
