<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModOpcion extends Model
{
    protected $table = 'opciones';
    use HasFactory;

    protected $primaryKey = 'OPC_id';
    public $incrementing = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['OPC_id', 'OPC_opciones', 'estado', 'createdAt', 'updatedBy', 'updatedAt', 'updatedBy', 'deletedBy', 'deletedAt'];
}
