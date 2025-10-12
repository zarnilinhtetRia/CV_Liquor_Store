<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exchange;

class ExchangeController extends Controller
{
    public function index(){
        $rate = Exchange::first();
        return view('exchange.exchange',compact('rate'));
    }

    public function save(Request $request){
        $rate = Exchange::first();
        if($rate){
            $rate->update($request->all());
        }else{
            Exchange::create($request->all());
        }
        return redirect()->back()->with('success','Exchange Rate Successfully Updated!');
    }
}
