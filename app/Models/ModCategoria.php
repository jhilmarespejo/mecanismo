<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModCategoria extends Model
{
    protected $table = 'categorias';
    use HasFactory;

    protected $primaryKey = 'CAT_id';
    public $incrementing = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['CAT_id', 'CAT_categoria', 'FK_CAT_id', 'FK_CAT_id', 'estado', 'createdAt', 'updatedBy', 'updatedAt', 'updatedBy', 'deletedBy', 'deletedAt'];
}
