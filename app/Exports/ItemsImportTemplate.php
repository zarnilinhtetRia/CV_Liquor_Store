<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Item;
use Illuminate\Support\Collection;

class ItemsImportTemplate implements WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    /**
     * @var Item $item
     */

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Location',
            'Product Name',
            'Variation Description',
            'Product Description',
            'Model',
            'Colour',
            'Stock Type',
            'Product Unit',
            'Quantity',
            'Barcode',
            'Product Code',
            'Alert Quantity',
            'Expired Date',
            'Stock Agent Date',
            'Product Register Type',
            'Purchase Price',

            'Current Price',
            'Previous Price',
            'Retail Price',
            'Wholesale Price',
            'Cost Price',
        ];
    }
}
