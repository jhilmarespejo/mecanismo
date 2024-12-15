<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModTipoEstablecimiento extends Model
{
    protected $table = 'tipo_establecimientos';
    use HasFactory;


    protected $primaryKey = 'TES_id';
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
        // static::deleting(function ($model) {
        //     $model->deletedBy = Auth::id();
        //     $model->deletedAt = now();
        // });
    }



}
