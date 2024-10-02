<?php

namespace App\Http\Controllers;
use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(){
        if(auth::check() &&  auth::user()->admin == 1){
            $alluser = user::getAll();
            $totalUsers = $alluser->count();
            return  view('Admin.users',['alluser'=>$alluser],['totalUsers'=>$totalUsers]);
        }else{
            return  redirect()->route('login')->with('error','bạn không phải là người quản trị');
        }
    }
    public function indexAddUser(){
        if(auth::check()&& auth::user()->admin == 1){
            return view("Admin.AddUser");
        }
        return  redirect()->route('login')->with('error','bạn không phải là người quản trị');
    }
    public function create(Request $request)
    {
        if(auth::check() && auth::user()->admin == 1) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'admin' => 'required|in:0,1' // Đảm bảo admin chỉ nhận giá trị 0 hoặc 1
            ]);
            
            // Tạo một đối tượng User mới và lưu vào cơ sở dữ liệu
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->admin = $request->admin; 
            $user->save();
    
            return redirect()->route('admin_users')->with('success', 'Người dùng đã được tạo thành công!');
        }
    
        return redirect()->route('login')->with('error', 'Bạn không phải là người quản trị');
    }
    
    public function deleteUser($id)
    {
        if(auth::check() &&  auth::user()->admin == 1){
            $deleted = User::deleteUserById($id);
            if ($deleted) {
                return redirect()->back()->with('success', 'Người dùng đã được xóa thành công!');
            } else {
                return redirect()->back()->with('error', 'Không tìm thấy người dùng!');
            }
        }else{
            return  redirect()->route('login')->with('error','bạn không phải là người quản trị');
        }
        
    }
   
    public function edit($id)
        {
            if(auth::check() && auth::user()->admin == 1){
                $data = User::getRecordByID($id);
                if($data){
                    // Mã hóa lại mật khẩu gốc
                    return view("Admin.editUser", ['data' => $data]);
                }
            } else {
                return redirect()->route('login')->with('error', 'Bạn không phải là người quản trị');
            }
        }
      
    public function editUser(Request $request, $id)
        {
            if(auth::check() && auth::user()->admin == 1){
                // Validate dữ liệu nhập vào từ form
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email,'.$id,
                    'password' => 'nullable|string|min:6|confirmed',
                    'admin' => 'required|in:0,1'
                ]);
                
                // Lấy dữ liệu người dùng cần cập nhật
                $user = User::getRecordByID($id);
                
                if($user){
                    // Cập nhật các trường thông tin của người dùng
                    $user->name = $request->name;
                    $user->email = $request->email;
                    if($request->has('password')) {
                        $user->password = Hash::make($request->password);
                    }
                    $user->admin = $request->admin;
                    
                    // Lưu các thay đổi vào cơ sở dữ liệu
                    $user->save();
                    
                    // Redirect về trang danh sách người dùng hoặc trang chi tiết người dùng
                    return redirect()->route('admin_users')->with('success', 'Thông tin người dùng đã được cập nhật thành công.');
                } else {
                    return redirect()->back()->with('error', 'Không tìm thấy người dùng có ID ' . $id);
                }
            } else {
                return redirect()->route('login')->with('error', 'Bạn không phải là người quản trị');
            }
        }
    public function SearchUser(Request $request){
        if (Auth::check() && auth::user()->admin == 1 ) {
            $search = $request->input('search');
            $alluser = User::getnameuser($search);
            $totalUsers = $alluser->count();
            return view("Admin.searchUser",['alluser'=>$alluser],['totalUsers'=>$totalUsers]);

        }else{
            return redirect()->route('login')->with('error', 'Bạn không phải là người quản trị');
        }

    }

}
