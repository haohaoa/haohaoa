<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class saerchController extends Controller
{
    public function index(){
        $search = request('search');
        $allsearch = Product::getnameProduct($search);
        return view('frontend.search', ['allsearch'=> $allsearch]);
    }

}
