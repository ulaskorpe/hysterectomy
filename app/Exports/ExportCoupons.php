<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportCoupons implements FromArray, WithHeadings, WithMapping, WithStyles
{

    protected $coupons;

    public function __construct(array $coupons)
    {
        $this->coupons = $coupons;
    }

    public function array(): array
    {
        return $this->coupons;
    }

    public function headings(): array
    {
        // Sabit başlıklar
        $defaultHeaders = [
            'ID',
            'Kod',
            'İndirim Tipi',
            'İndirim Tutarı',
            'Başlangıç',
            'Bitiş',
            'Kullanım Hakkı',
            'Kullanılan Adet',
            'Tüm Sepette Geçerli',
            'Grup',
            'Çek Olarak Kullanım',
            'Çek Kullanılan Miktar',
            'Ürün Tipleri'
        ];

        return $defaultHeaders;
    }

    public function map($row): array
    {
        // Sabit veriler
        $mappedRow = [
            $row['id'],
            $row['code'],
            $row['type'],
            $row['discount'],
            Carbon::parse($row['start_date'])->setTimezone('Europe/Istanbul')->format('d.m.Y'),
            Carbon::parse($row['end_date'])->setTimezone('Europe/Istanbul')->format('d.m.Y'),
            $row['usage_count'],
            $row['used_count'],
            $row['apply_cart'] && $row['apply_cart'] === true ? 'Evet' : 'Hayır',
            $row['group'] ? $row['group']['name'] : '',
            $row['as_voucher'] && $row['as_voucher'] === true ? 'Evet' : 'Hayır',
            $row['used_amount']
        ];

        $product_types_html = '';

        if( $row['product_types'] && count($row['product_types']) > 0 ){
            foreach ($row['product_types'] as $key => $item) {
                $product_types_html .= '• '. $item['name'];
                if( $key < ( count($row['product_types']) - 1 ) ){
                    $product_types_html .= PHP_EOL;
                }
            }
        }

        $mappedRow[] = $product_types_html;

        return $mappedRow;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('M1:M' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('M')->setWidth(80);
        // Tüm hücreler için dikey hizalamayı ortalayacak şekilde ayarlıyoruz
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return [
            // Style the first row as bold text.
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 13
                ]
            ],

            // Styling a specific cell by coordinate.
            //'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            //'C'  => ['font' => ['size' => 16]],
        ];
    }
}
