<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\contact;

class AdminHomeController extends Controller
{
    public function index(){
        if(Auth::check()){
            $user = Auth::user();
            $admin = $user->admin;
            if($admin == 1){
                $allContact = contact::getall();
                $tongallContact = $allContact->count(); 
                session(['tongallContact'=>$tongallContact]);
                return view('Admin.home',['allContact'=>$allContact]);
            }
            return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');
        }else{
            return Redirect::route('login');
        }
    }
}
