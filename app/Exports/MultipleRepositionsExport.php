<?php

namespace App\Exports;

use App\Models\Reposicion;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleRepositionsExport implements WithMultipleSheets
{
    protected $reposiciones;

    public function __construct($reposiciones)
    {
        $this->reposiciones = $reposiciones;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->reposiciones as $reposicion) {
            $sheets[] = new ReposicionExport($reposicion);
        }

        return $sheets;
    }

}
