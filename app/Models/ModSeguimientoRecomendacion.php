<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModSeguimientoRecomendacion extends Model
{
    protected $table = 'seguimiento_recomendaciones';
    use HasFactory;

    protected $primaryKey = 'SREC_id';
    public $incrementing = true;

    public $timestamps = false;
    protected $guarded = [];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->createdBy = Auth::id();
            $model->createdAt = now();
        });
        static::updating(function ($model) {
            $model->upatedBy = Auth::id();
            $model->updatedAt = now();
        });
    }
}