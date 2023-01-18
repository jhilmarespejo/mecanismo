<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModRecomendacion extends Model
{
    protected $table = 'recomendaciones';
    use HasFactory;

    protected $primaryKey = 'REC_id';
    public $incrementing = true;

    public $timestamps = false;
    protected $guarded = [];
    // protected $fillable = ['REC_recomendacion', 'FK_FRM_id'];
}
