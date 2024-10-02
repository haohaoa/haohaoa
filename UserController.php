<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use App\Models\user;
use App\Models\bill;
use App\Models\cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('frontend.login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lấy dữ liệu từ form
        $username = $request->input('user-name');
        $password = $request->input('user-password');

        // Kiểm tra thông tin đăng nhập
        $credentials = [
            'email' => $username,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            // Nếu thông tin đăng nhập đúng, lưu thông tin người dùng vào session
            $user = Auth::user();
            $request->session()->put('user', $user);
            $id = $user->id;
            $bill = bill::countUnseenBills();
            $cart = cart::getCartUserID($id);
            $dem = count($cart);
            session(['bill' => $bill]);
            session(['cart' => $cart]);
            session(['dem'=>$dem]);
            if($user->admin == 1){
                return redirect::route('admin_home');
            }
            // Chuyển hướng tới trang chính
            return Redirect::route('home');
            // return redirect(route('home'));
        } else {
            // Nếu thông tin đăng nhập sai, redirect lại trang đăng nhập với thông báo lỗi
            return redirect()->route('login')->with('error', 'Thông tin đăng nhập không chính xác.');
        }
  
    }
    public function register(Request $request)
    {
        try {
            
            // Kiểm tra dữ liệu đầu vào từ form đăng ký
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',//kiểm tra chỉ duy nhất 1 trường 
                'password' => 'required|string|min:6|confirmed',// Kiểm tra mật khẩu phải được xác nhận
            ]);
    
            // Tạo một người dùng mới và mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password); // Mã hóa mật khẩu
            $user->save();
    
            // Đăng nhập người dùng sau khi đăng ký thành công (tuỳ chọn)
            // auth()->login($user);
    
            // Chuyển hướng người dùng đến trang sau khi đăng ký thành công
            return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công!');
        } catch (\Exception $e) {
            // Xử lý bất kỳ ngoại lệ nào xảy ra và trả về một thông báo lỗi
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi đăng ký tài khoản: ' . $e->getMessage());

        }
       
    }
    


    /**
     * Display the specified resource.
     */
    public function show(user $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, user $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user $user)
    {
        Auth::logout(); // Đăng xuất người dùng
        return redirect()->route('login'); // Chuyển hướng về trang đăng nhập hoặc trang chính khác
    }
}
