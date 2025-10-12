<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    public function index()
    {

        $types = UserType::latest()->get();
        return view('user_type.user_type', compact('types'));
    }

    public function store(Request $request)
    {

        $type = new UserType();
        $type->fill($request->all());
        $type->save();
        return redirect()->back()->with('success', 'User Type Register Successfully!');
    }

    public function edit($id)
    {

        $types = UserType::find($id);

        return view('user_type.user_type_edit', compact('types'));
    }

    public function update($id, Request $request)
    {

        $types = UserType::find($id);
        $types->update($request->all());
        return redirect(url('user_type'))->with('success', 'User Type Update Successfully!');
    }

    public function delete($id)
    {

        $types = UserType::find($id);
        $types->delete();
        return redirect()->back()->with('delete', 'User Type Delete Successfully!');
    }
}
