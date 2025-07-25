<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $table = 'areas';

    protected $fillable = ['nombre', 'codigo', 'siglas'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
