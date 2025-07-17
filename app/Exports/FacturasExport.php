<?php

namespace App\Exports;

use App\Models\Factura;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class FacturasExport implements FromArray, WithHeadings, WithTitle, WithEvents
{

    protected $facturas;
    protected $montoSolicitud;

    public function __construct($facturas, $montoSolicitud)
    {
        $this->facturas = $facturas;
        $this->montoSolicitud = $montoSolicitud;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->facturas as $factura) {
            $solicitud = $factura->solicitud;

            $data[] = [
                'Solicitud ID' => $solicitud?->id ?? 'N/A',
                'Fecha Solicitud' => $solicitud?->fecha ?? 'N/A',
                'Área' => optional($solicitud?->area)->nombre ?? 'N/A',
                'Personas' => $solicitud?->personas ?? 'N/A',
                'Uso' => $solicitud?->uso ?? 'N/A',
                'Entregado' => $solicitud?->monto ?? 0,
                'Estado' => $solicitud?->estado ?? 'N/A',
                'Fecha Registro Fact' => $factura->fecha_registro,
                'Fecha Factura' => $factura->fecha_factura,
                'Proveedor' => $factura->proveedor,
                'Importe' => $factura->importe,
                'Situación' => $factura->situacion,
            ];
        }

        // Línea en blanco
        $data[] = [];

        // Totales
        $totalFacturas = $this->facturas->sum('importe');
        $diferencia = $this->montoSolicitud - $totalFacturas;

        $estado = match (true) {
            $diferencia > 1 => 'Falta recurso por comprobar',
            abs($diferencia) <= 1 => 'Solicitud comprobada exitosamente',
            default => 'Factura sobrepasa el total del recurso solicitado',
        };

        $data[] = ['Monto del Recurso Solicitado', $this->montoSolicitud];
        $data[] = ['Total Facturado', $totalFacturas];
        $data[] = ['Diferencia', $diferencia];
        $data[] = ['Estado', $estado];

        return $data;
    }

    public function headings(): array
    {
        return [
            'Solicitud ID',
            'Fecha Solicitud',
            'Área',
            'Personas',
            'Uso',
            'Entregado',
            'Estado',
            'Fecha Registro Fact',
            'Fecha Factura',
            'Proveedor',
            'Importe',
            'Situación',
        ];
    }

    public function title(): string
    {
        return 'Facturas'; // Título de la hoja en Excel
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Encabezados (fila 1) en negrita y fondo gris claro
                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'D3D3D3'],
                    ],
                    'borders' => [
                        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    ],
                ]);

                // Total y estados (últimas 4 filas)
                $highestRow = $sheet->getHighestRow();

                // Estilizar las 4 últimas filas (títulos en negrita y fondo amarillo claro)
                $sheet->getStyle("A" . ($highestRow - 3) . ":B" . $highestRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FFF9C4'], // amarillo claro
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    ],
                ]);
            },
        ];
    }

}
