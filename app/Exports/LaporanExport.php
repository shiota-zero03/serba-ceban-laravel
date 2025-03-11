<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets
{
    protected $dataSheet1;
    // protected $dataSheet2;

    public function __construct($dataSheet1)
    {
        $this->dataSheet1 = $dataSheet1;
        // $this->dataSheet2 = $dataSheet2;
    }
    public function sheets(): array
    {
        return [
            new LaporanOneExport($this->dataSheet1),
            new LaporanTwoExport($this->dataSheet1),
        ];
    }
}
