<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
    protected $table = 'opciones';
    use HasFactory;

    protected $fillable = ['OPC_opcion', 'FK_BCP_id'];

    public function BancoPreguntas()
    {
        return $this->belongsTo(BancoPreguntas::class);
    }
}
