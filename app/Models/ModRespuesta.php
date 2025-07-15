<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModRespuesta extends Model
{
    protected $table = 'respuestas';
    use HasFactory;
    
    protected $primaryKey = 'RES_id';
    public $incrementing = true;
    // public $timestamps = false;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $guarded = [];
    

    // protected static function boot() {
    //     parent::boot();
        
    //     static::creating(function ($model) {
    //         $model->createdBy = Auth::user()?->username;
    //         $model->createdAt = now();
    //     });
        
    //     static::updating(function ($model) {
    //         $model->updatedBy = Auth::user()?->username;
    //         $model->updatedAt = now();
    //     });
    
        
    // }

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $user = Auth::user();
            $model->createdBy = $user ? json_encode([
                'id' => $user->id,
                'username' => $user->username
            ]) : null;
            $model->createdAt = now();
        });

        static::updating(function ($model) {
            $user = Auth::user();
            $model->updatedBy = $user ? json_encode([
                'id' => $user->id,
                'username' => $user->username
            ]) : null;
            $model->updatedAt = now();
        });
        
        // static::deleting(function ($model) {
        //     $model->deletedBy = Auth::user()?->username;
        //     $model->deletedAt = now();
        // });
    }
}
