<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fondo extends Model
{

    use SoftDeletes;

    protected $table = 'fondos';
    
    protected $fillable = ['monto'];
}
