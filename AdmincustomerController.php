<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\product;
use App\Models\user;
use Illuminate\Support\Facades\Log;

class AdmincustomerController extends Controller
{
        public function index(){
            if(Auth::check()){
                $user = Auth::user();
                $admin = $user->admin;
                if($admin == 1){
                    $allbill = bill::orderBy('id', 'desc')->get();//giảm dần
                    foreach($allbill as $bill){
                        $userkh = user::getRecordByID($bill -> id_user);
                        $product = product::getProductById($bill->id_product);
                        $billData[] = [
                            'idBill' => $bill->id,
                            'idProduct' => $bill->id_product,
                            'img' => $product ->link,
                            'nameProduct'=> $product ->name,
                            'name'=> $userkh ->name,
                            'status' => $bill->status,
                            'quantity'=> $bill->quantity,
                        
                        ];
                        
                    
                    }         
                    return view('Admin.customer',['billData'=>$billData]);
                }
                return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');
            }else{
                return Redirect::route('login');
            }
        }
    public function indexDetali($id){
        if(auth::check()&& auth::user()->admin == 1){
                    bill::updateStatus($id);
                    $billcout = bill::countUnseenBills();
                    session(['bill' => $billcout]);
                    $bill = bill::getBillById($id);
                    $userkh = user::getRecordByID($bill -> id_user);
                    $product = product::getProductById($bill->id_product);
                    $billData[] = [
                        'idBill' => $bill->id,
                        'idProduct' => $bill->id_product,
                        'img' => $product ->link,
                        'nameProduct'=> $product ->name,
                        'name'=> $userkh ->name,
                        'status' => $bill->status,
                        'address' =>$bill ->address,
                        'email' =>$bill ->email,
                        'phone' =>$bill -> phone,
                        'money'=>$bill->money,
                        'note'=>$bill ->note,
                        'quantity'=> $bill->quantity,
                        'created_at'=>$bill->created_at,
                    ];
                    
                return view('Admin.billDetali',['billData'=>$billData]);
        }else{
            return redirect()->route('login')->with('thành công', 'bạn không phải là người quản trị');

        }
    }
    public function status($id, $id_product)
    {
        if (Auth::check() && Auth::user()->admin == 1) {
            try {
                // Cập nhật trạng thái đơn hàng
                 Bill::updateStatusDG($id);
                 $billl = Bill::getBillById($id);
                // Lấy thông tin sản phẩm
                $product = Product::getProductById($id_product);
                $currentQuantity = $product->quantyti;
                $billquantity = $billl ->quantity;
                // Tính toán số lượng mới
                $newQuantity = $currentQuantity - $billquantity;
   
                // Cập nhật số lượng sản phẩm
                $updated = Product::updateQuantity($id_product, $newQuantity);
    
                // Kiểm tra nếu cập nhật thành công
                if ($updated) {
                    Log::info("Product ID: $id_product updated successfully. New quantity: $newQuantity");
                    return redirect()->route('admin_customer')->with('success', 'Đã xác nhận đơn hàng thành công và cập nhật số lượng sản phẩm');
                } else {
                    Log::error("Failed to update product ID: $id_product. New quantity: $newQuantity");
                    return redirect()->route('admin_customer')->with('error', 'Đã xác nhận đơn hàng nhưng cập nhật số lượng sản phẩm thất bại');
                }
            } catch (\Exception $e) {
                Log::error("Error updating product ID: $id_product. Error: " . $e->getMessage());
                return redirect()->route('admin_customer')->with('error', 'Đã xảy ra lỗi trong quá trình xử lý đơn hàng');
            }
        } else {
            return redirect()->route('login')->with('error', 'Bạn không phải là người quản trị');
        }
    }
    

}
