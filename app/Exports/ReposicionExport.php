<?php

namespace App\Exports;

use App\Models\Reposicion;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ReposicionExport implements  FromArray, WithHeadings, WithTitle, WithEvents
{
    protected $reposicion;

    public function __construct(Reposicion $reposicion)
    {
        $this->reposicion = $reposicion;
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->reposicion->solicitudes as $solicitud) {
            foreach ($solicitud->facturas as $factura) {
                $data[] = [
                    $solicitud->id,
                    $solicitud->fecha,
                    optional($solicitud->area)->nombre ?? 'N/A',
                    $solicitud->personas,
                    $solicitud->uso,
                    $solicitud->monto,
                    $factura->fecha_registro,
                    $factura->fecha_factura,
                    $factura->proveedor,
                    $factura->id,
                    $factura->importe,
                    $factura->situacion,
                    $factura->c_c,
                    $factura->objeto_gasto,
                    $this->reposicion->n_revolvencia,
                    $solicitud->id,
                    $factura->c_c,
                    $this->reposicion->fecha_reg,
                    $factura->objeto_gasto,
                    optional($solicitud->area)->nombre ?? 'N/A',
                    $solicitud->uso,
                    $factura->id,
                    $factura->proveedor,
                    $factura->importe,
                ];
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            // Primera fila de encabezados (triple bloque)
            [
                'SOLICITUD DE RECURSOS', '', '', '', '', '', // A1-F1 (6 columnas)
                'COMPROBACION DOCUMENTAL', '', '', '', '', '', '', '', // G1-N1 (8 columnas)
                'REPOSICION', '', '', '', '', '', '', '', '', '', '' // O1-Y1 (10 columnas)
            ],
            // Segunda fila real de encabezados
            [
                'No.', 'FECHA SOLICITUD', 'AREA SOLICITANTE', 'PERSONA', 'CONCEPTO', 'IMPORTE ENTREGADO',
                'FECHA RECEPCION DOC', 'FECHA DOCUMENTO', 'RAZON SOCIAL', 'No. DOC.', 'IMPORTE COMPROBADO', 'SITUACION', 'CC', 'OG',
                'N°', 'NO. CONTROL', 'CENTRO DE COSTO', 'FECHA', 'OG', 'AREA SOLICITANTE', 'CONCEPTO', 'NO. FACTURA', 'PROVEEDOR', 'MONTO',
            ]
        ];
    }

    public function title(): string
    {
        return $this->reposicion->nombre_rep ?? 'Reposición';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // === Ajuste de ancho de columnas ===
                $columnWidths = [
                    6, 18, 22, 20, 25, 20,
                    22, 20, 22, 12, 22, 16, 8, 8,
                    6, 16, 22, 15, 10, 22, 25, 16, 22, 14,
                ];

                foreach ($columnWidths as $index => $width) {
                    $colLetter = Coordinate::stringFromColumnIndex($index + 1);
                    $sheet->getDelegate()->getColumnDimension($colLetter)->setWidth($width);
                }

                // Estilo de encabezados combinados
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('G1:N1');
                $sheet->mergeCells('O1:X1');

                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '2196F3']], // Azul
                ]);

                $sheet->getStyle('G1:N1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFEB3B']], // Amarillo
                ]);

                $sheet->getStyle('O1:X1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'C8E6C9']], // Verde
                ]);

                // Encabezados segunda fila
                $sheet->getStyle('A2:X2')->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                    'alignment' => ['horizontal' => 'center'],
                ]);
            }
        ];
    }
}
