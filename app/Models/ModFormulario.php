<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModFormulario extends Model
{
    protected $table = 'formularios';
    use HasFactory;
    
    protected $primaryKey = 'FRM_id';
    
    public $incrementing = true;
    

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    // protected $fillable = ['FRM_titulo', 'FRM_titulo', 'FRM_version', 'FRM_fecha', 'FK_EST_id'];
    protected $guarded = [];
    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            // $model->user = Auth::id();
            $model->createdAt = now();
        });
        static::updating(function ($model) {
            // $model->upatedBy = Auth::id();
            $model->updatedAt = now();
        });
        
    }
}
