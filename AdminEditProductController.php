<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
class AdminEditProductController extends Controller
{
    public function edit($request){
       if(auth::check() && auth::user()->admin == 1 ){
        $id = request('id');
        $product = product::getProductById($id);
        return  view("Admin.editproduct",['data'=>$product]);
       }else{
        return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');
       }
    }
    
    public function update(Request $request, $id)
{
    // Validate dữ liệu được gửi từ form
    $request->validate([
        'productName' => 'required|string|max:255',
        'productDescription' => 'required|string',
        'productPrice' => 'required|numeric',
        'productColor' => 'required|string|max:50',
        'productImage' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Đảm bảo file được tải lên là hình ảnh và có kích thước nhỏ hơn 2MB
        'isFeatured' => 'nullable|boolean', // Cho phép giá trị null và boolean (true/false)
    ]);

    // Lấy thông tin sản phẩm cần cập nhật
    $product = Product::findOrFail($id);

    // Kiểm tra xem có file hình ảnh mới được tải lên không
    if ($request->hasFile('productImage')) {
        // Xử lý tải lên hình ảnh và cập nhật đường dẫn mới
        $file = $request->file('productImage');
        $ext = $file->extension();
        $link = time() . '-' . 'product.' . $ext;
        $file->move(public_path('ruiz/assets/images/product'), $link);
        $product->link = $link; // Cập nhật đường dẫn mới
    }

    $data = [
        'name' => $request->input('productName'),
        'link' => isset($link) ? $link : $product->link, // Cập nhật link mới hoặc giữ nguyên link cũ
        'color' => $request->input('productColor'),
        'money' => $request->input('productPrice'),
        'description' => $request->input('productDescription'),
        'hot' => $request->has('isFeatured') ? true : false,
    ];

    // Lưu các thay đổi vào cơ sở dữ liệu
    $product->fill($data)->save();

    // Chuyển hướng và gửi thông báo thành công
    return redirect()->route('admin_product')->with('success', 'Product updated successfully');
}

}
