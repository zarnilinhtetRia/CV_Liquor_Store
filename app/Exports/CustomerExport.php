<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromQuery, WithMapping, WithHeadings
{
    protected $warehouseId;

    public function __construct($warehouseId = null)
    {
        $this->warehouseId = $warehouseId;
    }

    public function query()
    {
        $query = Customer::query()->with('warehouse');

        if ($this->warehouseId !== 'All Location' && !is_null($this->warehouseId)) {
            $query->where('warehouse_id', $this->warehouseId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Location',
            'Name',
            'Phone Number',
            'Email',
            'Address',
        ];
    }

    public function map($customer): array
    {
        return [
            'Location' => $customer->warehouse ? $customer->warehouse->name : 'All Location',
            'Name' => $customer->name,
            'Phone Number' => $customer->phno,
            'Email' => $customer->email,
            'Address' => $customer->address,
        ];
    }
}
