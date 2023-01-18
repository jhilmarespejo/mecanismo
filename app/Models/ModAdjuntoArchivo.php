<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModAdjuntoArchivo extends Model
{
    protected $table = 'r_adjuntos_archivos';
    use HasFactory;

    protected $primaryKey = 'RAA_id';
    public $incrementing = true;
    // const CREATED_AT = 'createdAt';
    // const UPDATED_AT = 'updatedAt';
    public $timestamps = false;
    protected $fillable = ['RAA_id', 'FK_ARC_id', 'FK_ADJ_id'];
}
