<?php

namespace App\Http\Controllers;
use App\Models\cart;
use App\Models\bill;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class billController extends Controller
{
    public function index()
    {
        if(auth::check()){
            $user = auth::user();
            $cart = Cart::getCartUserID($user->id);
            return view('frontend.bill', ['cart' => $cart, 'user' => $user]);
        } else {
            return redirect()->route('login')->with('error', 'Bạn chưa đăng nhập');
        }
    }
    
        
    public function addBill(Request $request)
    {
    if(auth::check()){
        // Lấy thông tin người dùng đang đăng nhập
        $user = auth::user();
        $id_user = $user->id;

        // Lấy giỏ hàng của người dùng
        $cart = Cart::getCartUserID($id_user);

        // Kiểm tra xem giỏ hàng có dữ liệu hay không
        if($cart->isNotEmpty()) {
            // Lặp qua từng mục trong giỏ hàng để tạo đơn hàng cho mỗi mục
            foreach($cart as $ct) {
                // Tạo dữ liệu đơn hàng từ thông tin người dùng và thông tin sản phẩm trong giỏ hàng
                $data = [
                    'id_product' => $ct->product_id,
                    'money' => $ct->money,
                    'id_user' => $id_user,
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'address' => $request->input('address'),
                    'phone' => $request->input('phone'),
                    'note' => $request->input('note'),
                    'quantity' => $ct->quantity,
                    'created_at' => Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString(),
                    'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString(),
                ];

                // Thêm bản ghi mới vào bảng bill bằng cách gọi phương thức từ model
                bill::getAddbill($data);
            }

            // Xóa giỏ hàng sau khi đã đặt hàng thành công
            Cart::deleteUserCart($id_user);
            $cart = Cart::getCartUserID($id_user);
            session(['cart'=> $cart]);
            session(['dem'=>count($cart)]);
            // Chuyển hướng người dùng đến trang cảm ơn hoặc trang khác
            return redirect()->route('success')->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
        } else {
            // Nếu giỏ hàng rỗng, thông báo lỗi và chuyển hướng người dùng
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống!');
        }
    } else {
        // Nếu người dùng chưa đăng nhập, chuyển hướng về trang đăng nhập
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
    }
    }
    
    public function history($id){
        if(auth::check()){
            $bill = Bill::getHistory($id);
            $all = [];
            foreach ($bill as $bill){
                $product = Product::getProductById($bill->id_product);
                $all[] = [
                    'id' => $bill->id,
                    'order_code' => $bill->order_code,
                    'product_name' => $product->name, // Get product name
                    'product_link' => $product->link, // Get product link
                    'address'=>$bill->address,
                    'money' => $bill->money,
                    'status' => $bill->status,
                    'quantity' => $bill->quantity,
                    'created_at' => $bill->created_at
                    
                ];
            }
            return view('frontend.history', ['all' => $all]);
        } else {
            return redirect()->route('login')->with('error', 'Please log in to continue!');
        }
    }
    
    
}
