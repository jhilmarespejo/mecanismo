<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModEstablecimientoInfo extends Model
{
    protected $table = 'establecimientos_info';
    use HasFactory;
    
    protected $primaryKey = 'EINF_id';
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
