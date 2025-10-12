<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemsUpdateImport implements ToModel
{
    private $firstRowSkipped = false;
    private $hasErrors = false;
    private $skipImport = false;
    private $warehouseId;
    protected $rowCount = 0;
    public function __construct($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    public function model(array $row)
    {
        $this->rowCount++;
        if (!$this->firstRowSkipped) {
            $this->firstRowSkipped = true;
            return null;
        }
        if ($this->hasErrors) {
            return null;
        }
        if (empty($row[8])) {
            $row[8] = $this->generateRandomBarcode();
        }

        $item = Item::where('warehouse_id', $this->warehouseId)
            ->where('item_name', $row[3])
            ->first();

        if ($item) {
            $item->update([
                'retail_price' => $row[6] ?? $item->retail_price,
                'wholesale_price' => $row[7] ?? $item->wholesale_price,
                // 'buy_price' => $row[6] ?? $item->buy_price,
            ]);
        } else {
            Log::error('Item not found:', [
                'warehouse_id' => $this->warehouseId,
                'item_name' => $row[3],
            ]);
        }

        return null;
    }



    public function generateRandomBarcode()
    {
        $barcodeBase = str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
        $checksum = $this->calculateEAN13Checksum($barcodeBase);
        return $barcodeBase . $checksum;
    }

    public function formatBarcode($barcode)
    {
        $barcode = str_pad($barcode, 12, '0', STR_PAD_LEFT);
        if (strlen($barcode) == 13) {
            return $barcode;
        } else {
            $checksum = $this->calculateEAN13Checksum($barcode);
            return $barcode . $checksum;
        }
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
