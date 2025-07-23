<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use SoftDeletes;
    
    protected $table = 'solicitudes';
    
    protected $fillable = [
        'id',
        'fecha',
        'area',
        'personas',
        'uso',
        'monto',
        'saldo_restante',
        'estado'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function reposicion()
    {
        return $this->belongsTo(Reposicion::class);
    }

}
