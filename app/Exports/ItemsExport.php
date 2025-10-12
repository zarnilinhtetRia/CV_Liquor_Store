<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Item;

class ItemsExport implements FromQuery, WithMapping, WithHeadings
{
    protected $warehouseId;

    public function __construct($warehouseId = null)
    {
        $this->warehouseId = $warehouseId;
    }

    public function query()
    {
        if ($this->warehouseId == 'All Location' || is_null($this->warehouseId)) {
            return Item::with(['variations', 'warehouse', 'item_quantity']);
        } else {
            return Item::with(['variations', 'warehouse', 'item_quantity'])
                ->whereHas('warehouse', function ($query) {
                    $query->where('id', $this->warehouseId);
                });
        }
    }

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

    public function map($item): array
    {
        $mappedData = [];

        foreach ($item->variations as $key => $variation) {
            $quantity = $item->item_quantity->firstWhere('variation_id', $variation->id);

            $mappedData[] = [
                'Location' => $item->warehouse ? $item->warehouse->name : 'All Location',
                'Product Name' => $item->item_name,
                'Variation Description' => $variation->variation_desc,
                'Product Description' => $item->item_descriptions,
                'Model' => $variation->model,
                'Colour' => $variation->colour,
                'Stock Type' => $item->stock_type,
                'Product Unit' => $variation->unit,
                'Quantity' => $quantity->warehouse_qty ?? 0,
                'Barcode' => $variation->barcode,
                'Product Code' => $variation->product_code,
                'Alert Quantity' => $quantity->alert_qty ?? 0,
                'Expired Date' => $variation->expired_date,
                'Stock Agent Date' => $variation->stock_agent_date,
                'Product Register Type' => $item->item_type,
                'Purchase Price' => $variation->buy_price,
                // 'Wholesale Price' => $variation->wholesale_price,
                'Current Price' => $variation->retail_price,
                'Previous Price' => $variation->retail_set_price,
                'Retail Price' => $variation->promotion_retail_unit,
                'Wholesale Price' => $variation->promotion_retail_set,
                'Cost Price' => $variation->cost_price,
            ];
        }

        return $mappedData;
    }
}
