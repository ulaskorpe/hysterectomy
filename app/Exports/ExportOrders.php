<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportOrders implements FromArray, WithHeadings, WithMapping, WithStyles
{

    protected $orders;

    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function array(): array
    {
        return $this->orders;
    }

    public function headings(): array
    {
        // Sabit başlıklar
        $defaultHeaders = [
            'ID',
            'Sipariş No',
            'Tarih',
            'Durumu',
            'İsim',
            'E-Posta',
            'Ara Toplam',
            'Toplam',
            'Kupon',
            'Kampanya',
            'Paraşüt Fatura ID',
            'Paraşüt E-Arşiv ID',
            'Ürünler'
        ];

        return $defaultHeaders;
    }

    public function map($row): array
    {
        $status = $row['status']['name'];
        $name = isset($row['cart_details']['extra_info']['billing']) ? $row['cart_details']['extra_info']['billing']['name'] : 'NA';
        $email = isset($row['cart_details']['extra_info']['billing']) ? $row['cart_details']['extra_info']['billing']['email'] : 'NA';

        // Sabit veriler
        $mappedRow = [
            $row['id'],
            $row['code'],
            Carbon::parse($row['created_at'])->setTimezone('Europe/Istanbul'),
            $status,
            $name,
            $email,
            $row['subtotal'],
            $row['total'],
            $row['coupon'] ? $row['coupon']['code'] : '',
            $row['campaign'] ? $row['campaign']['name'] : '',
            $row['parasut_fatura_id'],
            $row['parasut_earsiv_id'],
        ];

        $urunler_html = '';

        $keys = array_keys($row['cart_details']['items']);
        foreach ($row['cart_details']['items'] as $key => $item) {
            
            $urunler_html .= '• '. $item['quantity'].' x '.$item['title'];
            
            if($item['extra_info']['variant_details']){
                            
                foreach ($item['extra_info']['variant_details']['option_values'] as $option) {
                    $urunler_html .= '  #';
                    $urunler_html .= is_array($option['value']) ? $option['value'][0] : $option['value'];
                }
            
            }

            if( $key !== end($keys) ){
                $urunler_html .= PHP_EOL;
            }
            

        }

        $mappedRow[] = $urunler_html;

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
