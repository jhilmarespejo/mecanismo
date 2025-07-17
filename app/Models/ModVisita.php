<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModVisita extends Model{
    protected $table = 'visitas';
    use HasFactory;

    protected $primaryKey = 'VIS_id';
    public $incrementing = true;
    
    // public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $guarded = [];

    protected static function boot() {
        parent::boot();
        
        static::creating(function ($model) {
            $model->createdBy = Auth::user()?->id;
            $model->createdAt = now();
        });
        
        static::updating(function ($model) {
            $model->updatedBy = Auth::user()?->id;
            $model->updatedAt = now();
        });
        
        // static::deleting(function ($model) {
        //     $model->deletedBy = Auth::user()?->id;
        //     $model->deletedAt = now();
        // });
    }


    
}

