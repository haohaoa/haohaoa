<?php

namespace App\Http\Controllers;
use App\Models\product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(){
        $allproduct = product::GetAllProduct();
        return view('frontend.shop',['allproduct'=> $allproduct]);
    }
}
