<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModRecomendacion extends Model
{
    protected $table = 'recomendaciones';
    use HasFactory;

    protected $primaryKey = 'REC_id';
    public $incrementing = true;

    // public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $guarded = [];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->createdBy =Auth::user()?->id_usuario_dp;
            $model->createdAt = now();
        });
        static::updating(function ($model) {
            $model->updatedBy =Auth::user()?->id_usuario_dp;
            $model->updatedAt = now();
        });
        // static::deleting(function ($model) {
        //     $model->deletedBy =Auth::user()?->id_usuario_dp;
        //     $model->deletedAt = now();
        // });
    }
}
