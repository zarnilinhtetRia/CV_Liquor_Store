<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->get();
        return view('unit.unit', compact('units'));
    }

    public function unit_store(Request $request)
    {
        Unit::create($request->all());
        return redirect(url('unit'))->with('success', 'Unit Create Successful');
    }

    public function edit($id)
    {
        $units = Unit::find($id);
        return view('unit.unitEdit', compact('units'));
    }

    public function update(Request $request, $id)
    {
        $units = Unit::find($id);
        $units->update($request->all());
        return redirect(url('unit'))->with('success', 'Unit Update Successful');
    }

    public function delete($id)
    {
        $unit = Unit::find($id);
        $unit->delete();
        return redirect()->back()->with('delete', 'Unit Delete Successful');
    }
    public function get_part_data_unit()
    {

        $units = Unit::all();

        return response()->json($units);
    }
}
