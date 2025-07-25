<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reposicion extends Model
{

    use SoftDeletes;
    protected $table = 'reposiciones';

    protected $fillable = ['nombre_rep', 'n_revolvencia', 'fecha_reg'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

    public function solicitudGenerada()
    {
        return $this->hasOne(Solicitud::class, 'reposicion_generada_id');
    }
}
