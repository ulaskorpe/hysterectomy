<?php

namespace App\Exports;

use App\Models\ProductType;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProductTypeSales implements FromArray, WithHeadings, WithMapping, WithStyles
{

    protected $sales;
    protected $productType;

    public function __construct(array $sales, ProductType $productType)
    {
        $this->sales = $sales;
        $this->productType = $productType;
    }

    public function array(): array
    {
        return $this->sales;
    }

    public function headings(): array
    {
        // Sabit başlıklar
        $defaultHeaders = [
            'ID',
            'Sipariş ID',
            'Fiyat',
            'Ödenen',
            'Satış Tarihi',
            'İsim',
            'Adet',
            'Kupon',
            'Ad Soyad',
            'Email',
            'Telefon',
            'İl / İlçe'
        ];

        $dynamicHeaders = [];

        if( $this->productType->option_group ){
            foreach ($this->productType->option_group->options as $key => $option) {
                $dynamicHeaders[] = $option['name'];
            }
        }

        // Sabit başlıklar ile dinamik JSON başlıklarını birleştiriyoruz
        return array_merge($defaultHeaders, $dynamicHeaders);
    }

    public function map($row): array
    {
        $adsoyad = isset($row['order']['cart_details']['extra_info']['billing']) ? $row['order']['cart_details']['extra_info']['billing']['name'] : 'NA';
        $email = isset($row['order']['cart_details']['extra_info']['billing']) ? $row['order']['cart_details']['extra_info']['billing']['email'] : 'NA';
        $phone = isset($row['order']['cart_details']['extra_info']['billing']) ? $row['order']['cart_details']['extra_info']['billing']['phone'] : 'NA';
        $il_ilce = isset($row['order']['cart_details']['extra_info']['billing']) ? $row['order']['cart_details']['extra_info']['billing']['county'].' / '.$row['order']['cart_details']['extra_info']['billing']['state'] : 'NA';

        // Sabit veriler
        $mappedRow = [
            $row['id'],
            $row['order_id'],
            $row['price'],
            $row['discount'] ? $row['price'] - $row['discount'] : $row['price'],
            Carbon::parse($row['created_at'])->setTimezone('Europe/Istanbul')->format('d-m-Y H:i:s'),
            $row['product'] ? $row['product']['name'] : 'Ürün Bulunamadı',
            $row['count'],
            $row['order']['coupon'] && $row['discount'] > 0 ? $row['order']['coupon']['code'] : '',
            $adsoyad,
            $email,
            $phone,
            $il_ilce,
        ];

        if( $this->productType->option_group ){
            foreach ($this->productType->option_group->options as $key => $option) {
                
                $rowJsonValue = null;
                if( $row['product_variant'] ){

                    $head_value = Arr::first($row['product_variant']['option_values'], function ($value, $key) use ($option) {
                        return $value['id'] === $option->id;
                    });

                    if( $head_value ){
                        $rowJsonValue = $head_value['value'];
                    }

                }

                $mappedRow[] = $rowJsonValue;

            }
        }


        return $mappedRow;
    }

    public function styles(Worksheet $sheet)
    {
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
