<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;

class ReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $orders;
    protected $startDate;
    protected $endDate;
    protected $totalOmset;
    protected $totalKeuntungan;

    public function __construct($orders, $startDate, $endDate, $totalOmset, $totalKeuntungan)
    {
        $this->orders = $orders;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalOmset = $totalOmset;
        $this->totalKeuntungan = $totalKeuntungan;
    }

    public function collection(): Collection
    {
        $data = collect();

        // Add header info
        $data->push(['LAPORAN PENJUALAN BOOKSTORE']);
        $data->push(['Periode: ' . ($this->startDate ?? 'Awal') . ' s/d ' . ($this->endDate ?? 'Akhir')]);
        $data->push(['Tanggal Cetak: ' . now()->format('d M Y, H:i') . ' WIB']);
        $data->push([]);

        // Add summary section
        $data->push(['RINGKASAN']);
        $data->push(['Total Omset', 'Rp ' . number_format($this->totalOmset, 0, ',', '.')]);
        $data->push(['Total Keuntungan', 'Rp ' . number_format($this->totalKeuntungan, 0, ',', '.')]);
        $data->push(['Total Transaksi', count($this->orders)]);
        $data->push([]);

        // Add items table header
        $data->push(['No', 'Kode Pesanan', 'Judul Buku', 'Qty', 'Harga Satuan', 'Subtotal']);

        // Add items
        $itemNo = 1;
        foreach ($this->orders as $order) {
            if ($order->orderDetails && count($order->orderDetails) > 0) {
                foreach ($order->orderDetails as $detail) {
                    $data->push([
                        $itemNo++,
                        $order->kode_pesanan,
                        $detail->book->nama_buku ?? 'Buku',
                        $detail->qty,
                        (float) $detail->harga_satuan,
                        (float) $detail->subtotal,
                    ]);
                }
            }
        }

        // Add totals
        $data->push([]);
        $data->push(['TOTAL OMSET', '', '', '', '', (float) $this->totalOmset]);
        $data->push(['TOTAL KEUNTUNGAN', '', '', '', '', (float) $this->totalKeuntungan]);

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header style
        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Summary heading style
        $summaryRow = 5;
        $sheet->getStyle("A{$summaryRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
        ]);

        // Summary data style
        $sheet->getStyle("A" . ($summaryRow + 1) . ":B" . ($summaryRow + 3))->applyFromArray([
            'font' => ['bold' => true],
            'border' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Table header style
        $tableHeaderRow = 10;
        $sheet->getStyle("A{$tableHeaderRow}:F{$tableHeaderRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'border' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Table data style
        $dataStartRow = $tableHeaderRow + 1;
        $dataEndRow = $lastRow - 3;
        if ($dataEndRow >= $dataStartRow) {
            $sheet->getStyle("A{$dataStartRow}:F{$dataEndRow}")->applyFromArray([
                'border' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Alternate row colors
            for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
                if (($row - $dataStartRow) % 2 == 0) {
                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                    ]);
                }
            }
        }

        // Totals style
        $totalStartRow = $dataEndRow + 2;
        $sheet->getStyle("A{$totalStartRow}:F" . ($totalStartRow + 1))->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'border' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);

        // Format currency columns
        $sheet->getStyle("E{$dataStartRow}:F{$dataEndRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("B" . ($summaryRow + 1) . ":B" . ($summaryRow + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("F{$totalStartRow}:F" . ($totalStartRow + 1))->getNumberFormat()->setFormatCode('#,##0.00');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        return [];
    }
}
