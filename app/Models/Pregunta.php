<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pregunta extends Model
{
    use HasFactory;
    protected $table = 'r_bpreguntas_formularios';

    protected $primaryKey = 'RBF_id';
    public $incrementing = true;
   
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
