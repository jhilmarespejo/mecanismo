<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModVisita extends Model
{
    protected $table = 'visitas';
    use HasFactory;

    protected $primaryKey = 'VIS_id';
    public $incrementing = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $guarded = [];


}
