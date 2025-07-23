<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reposicion extends Model
{

    use SoftDeletes;
    protected $table = 'reposiciones';

    protected $fillable = ['nombre_rep', 'n_revolvencia', 'fecha_reg', 'o_g'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

}
