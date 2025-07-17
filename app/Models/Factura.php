<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fecha_registro', 'fecha_factura', 'solicitud_id',
        'factura', 'proveedor', 'concepto_gasto', 'situacion',
        'importe', 'objeto_gasto', 'c_c'
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
