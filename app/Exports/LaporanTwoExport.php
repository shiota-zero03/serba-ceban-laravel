<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanTwoExport implements FromView, WithStyles, WithColumnWidths, WithEvents, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('Export.Laporan.Sheet2', [
            'dateData' => $this->data['dateData'],
            'dateFormat' => $this->data['dateFormat'],
            'result' => $this->data['result'],
        ]);
    }

    // ✅ Set nama sheet
    public function title(): string
    {
        return 'Produk Harian';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  // No
            'B' => 25, // Nama Supplier
            'C' => 15, // Kode Produk
            'D' => 25, // Nama Produk
        ];
    }

    // ✅ Styling tabel (Border & Align Text)
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:Z')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A:Z')->getAlignment()->setVertical('center');
        // Border untuk semua cell
        $sheet->getStyle('A1:Z1000')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    }

    // ✅ Freeze Pane pada baris ke-2
    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $event->sheet->getDelegate()->freezePane('A3'); // ✅ Membekukan hingga baris kedua
            },
        ];
    }
}
