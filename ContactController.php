<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\contact;

class ContactController extends Controller
{
    public function index(){
       
        return view('frontend.contact');
    }
    public function contact(Request $request) {
        if (Auth::check()) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'id_bill' => $request->ib_bill,
                'message' => $request->message,
            ];
    
            $check = Contact::addContact($data);
            if ($check) {
                return redirect()->route('index_contact')->with('success', 'Bạn gửi yêu cầu thành công');
            } else {
                return redirect()->route('index_contact')->with('error', 'Bạn gửi yêu cầu thất bại');
            }
        } else {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để gửi yêu cầu');
        }
    }
    
}
