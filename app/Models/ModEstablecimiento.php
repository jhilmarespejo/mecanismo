<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModEstablecimiento extends Model
{
    protected $table = 'establecimientos';
    use HasFactory;

    protected $primaryKey = 'EST_id';
    public $incrementing = true;


    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];


}
