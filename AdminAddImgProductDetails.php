<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class AdminAddImgProductDetails extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->admin == 1) {
            $id = request('id');
            return view('Admin.AddImgProductDetails', ['id' => $id]);
        }else{
            return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');
        }
    }

    public function create(Request $request)
    {
        if (Auth::check() && Auth::user()->admin == 1) {
            // Kiểm tra xem có file được tải lên không
            if ($request->hasFile('productImage')) {
                // Lấy đối tượng của file được tải lên
                $file = $request->file('productImage');

                // Lấy phần mở rộng của file
                $ext = $file->extension();

                // Tạo tên mới cho file, kết hợp thời gian hiện tại và phần mở rộng của file
                $link = time() . '-' . 'product.' . $ext;

                // Di chuyển file vào thư mục 'uploads' trong thư mục 'public'
                $file->move(public_path('ruiz/assets/images/product'), $link);
            } else {
                // Nếu không có file được tải lên, đặt giá trị của $link là null
                $link = null;
            }
            $id= request('id');
            $data = [
                'product_id' =>  $id,
                'link' => $link
            ];
            $ProductDetail = ProductDetail::addIMGProductDetail($data);
            if ($ProductDetail) {
                // Nếu thành công, chuyển hướng về trang tạo sản phẩm với thông báo thành công
                return redirect()->route('admin_add_IMG_product_details', ['id' => $id])->with('success', 'Photo added successfully');
            } else {
                // Nếu có lỗi, chuyển hướng về trang tạo sản phẩm với thông báo lỗi
                return redirect()->back()->with('error', 'Failed to create product');
            }
        }
    }
}
