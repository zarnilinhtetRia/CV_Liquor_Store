<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\BrandVariations;
use App\Models\Customer;
use App\Models\Item;
use App\Models\ItemQuantity;
use App\Models\ItemVariation;
use App\Models\Warehouse;
use DateInterval;
use DateTime;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerImport implements ToModel
{
    private $firstRowSkipped = false;
    private $warehouseId; // Store the warehouse_id
    protected $rowCount = 0;

    public function __construct($warehouseId)
    {
        $this->warehouseId = $warehouseId; // Assign the warehouse_id from the controller
    }



    // private function convertExcelDate($value)
    // {
    //     if (is_numeric($value)) {
    //         $baseDate = new DateTime('1899-12-30');
    //         return $baseDate->add(new DateInterval("P{$value}D"))->format('Y-m-d');
    //     }

    //     return $value;
    // }

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


        $customer = Customer::where('name', $row[1])
            ->where('phno', $row[2])
            ->where('email', $row[3])
            ->where('branch', $warehouse->id)
            ->first();

        // info($item);

        if ($customer) {
        } else {
            $customer = new Customer([
                'branch' => $warehouse->id,
                'name' => $row[1],
                'phno' => $row[2],
                'email' => $row[3],
                'address' => $row[4],

            ]);
            $customer->save();
        }
    }


    public function getRowCount()
    {
        return $this->rowCount;
    }
}
