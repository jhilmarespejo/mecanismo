<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModBancoPregunta extends Model
{
    protected $primaryKey = 'BCP_id';
    public $incrementing = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $table = 'banco_preguntas';
    use HasFactory;
    protected $fillable = ['BCP_id', 'BCP_pregunta', 'BCP_tipoRespuesta', 'FK_CAT_id',
'BCP_complemento', 'BCP_aclaracion', 'BCP_opciones', 'createdBy', 'createdAt', 'updatedBy', 'updatedAt', 'deletedBy', 'deletedAt'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function opcion()
    {
        return $this->hasMany(Opcion::class);
    }


}
