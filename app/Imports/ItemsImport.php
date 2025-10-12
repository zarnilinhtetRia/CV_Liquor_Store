<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\BrandVariations;
use App\Models\Item;
use App\Models\ItemQuantity;
use App\Models\ItemVariation;
use App\Models\Warehouse;
use DateInterval;
use DateTime;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemsImport implements ToModel
{
    private $firstRowSkipped = false;
    private $warehouseId; // Store the warehouse_id
    protected $rowCount = 0;

    public function __construct($warehouseId)
    {
        $this->warehouseId = $warehouseId; // Assign the warehouse_id from the controller
    }



    private function convertExcelDate($value)
    {
        if (is_numeric($value)) {
            $baseDate = new DateTime('1899-12-30');
            return $baseDate->add(new DateInterval("P{$value}D"))->format('Y-m-d');
        }

        return $value;
    }

    public function model(array $row)
    {
        $this->rowCount++;

        // Skip the first row (headings)
        if (!$this->firstRowSkipped) {
            $this->firstRowSkipped = true;
            return null;
        }

        if ($this->warehouseId == 'All Location') {
            $warehouse = Warehouse::where('name', $row[0])->first();
        } else {
            $warehouse = Warehouse::where('name', $row[0])->first();
            if (!$warehouse || $warehouse->id != $this->warehouseId) {
                return null;
            }
        }

        if (empty($row[9])) {
            $row[9] = $this->generateRandomBarcode();
        }

        $item = Item::where('item_name', $row[1])
            ->where('stock_type', $row[6])
            ->where('item_descriptions', $row[3])
            ->where('warehouse_id', $warehouse->id)
            ->first();

        // info($item);

        if ($item) {
        } else {
            $item = new Item([
                'warehouse_id' => $warehouse->id,
                'item_name' => $row[1],
                'item_descriptions' => $row[3],
                'stock_type' => $row[6],
                'item_type' => $row[14],
                'parent_id' => 0,
            ]);
            $item->save();
        }


        $variation = ItemVariation::where('item_id', $item->id)
            ->where('product_code', $row[10])
            ->where('model', $row[4])
            ->where('colour', $row[5])
            ->where('variation_desc', $row[2])
            ->first();
        // info($variation);

        if ($variation) {
            $variation->retail_set_price = $row[18];
            $variation->promotion_retail_unit = $row[19];
            $variation->promotion_retail_set = $row[20];
            $variation->buy_price = $row[15];
            $variation->wholesale_price = $row[16];
            $variation->retail_price = $row[17];
            // $variation->cost_price = $row[21];
            $variation->save();
        } else {
            $variation = new ItemVariation([
                'item_id' => $item->id,
                'brand_variation_id' => $brand_variation->id ?? null,
                'variation_desc' => $row[2],
                'model' => $row[4],
                'colour' => $row[5],
                'unit' => $row[7],
                'barcode' => $row[9],
                'product_code' => $row[10] ?? null,
                'expired_date' => $this->convertExcelDate($row[12]) ?? null,
                'stock_agent_date' => $this->convertExcelDate($row[13]) ?? null,
                'buy_price' => $row[15] ?? 0,
                // 'wholesale_price' => $row[16] ?? 0,
                'retail_price' => $row[16] ?? 0,
                'retail_set_price' => $row[17] ?? 0,
                'promotion_retail_unit' => $row[18] ?? 0,
                'promotion_retail_set' => $row[19] ?? 0,
                'cost_price' => $row[20] ?? 0,
                'status' => 1,
            ]);
            $variation->save();
        }


        $quatity = ItemQuantity::where('item_id', $item->id)
            ->where('variation_id', $variation->id)
            ->first();
        // info($quatity);

        if ($quatity) {
            // $newWarehouseQty = $row[8];
            // $quatity->warehouse_qty += $newWarehouseQty;
            // $quatity->save();

            $oldWarehouseQty = $quatity->warehouse_qty;
            $newWarehouseQty = $row[8];
            $quatity->warehouse_qty = $newWarehouseQty;
            $quatity->available_qty += $newWarehouseQty - $oldWarehouseQty;
            $quatity->save();
        } else {
            $item_qty = new ItemQuantity([
                // 'item_id' => $item->id,
                // 'variation_id' => $variation->id,
                // 'warehouse_qty' => $row[8],
                // 'alert_qty' => $row[11],

                'item_id' => $item->id,
                'variation_id' => $variation->id,
                'warehouse_qty' => $row[8],
                'available_qty' => $row[8],
                'deliver_qty' => '0',
                'alert_qty' => $row[11],
            ]);
            $item_qty->save();
        }
    }


    // Additional methods for barcode generation and checksum

    public function generateRandomBarcode()
    {
        $barcodeBase = str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
        $checksum = $this->calculateEAN13Checksum($barcodeBase);
        return $barcodeBase . $checksum;
    }

    public function calculateEAN13Checksum($barcode)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0 ? 1 : 3) * intval($barcode[$i]);
        }
        $mod = $sum % 10;
        return $mod === 0 ? 0 : 10 - $mod;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }
}
