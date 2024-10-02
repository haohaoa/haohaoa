<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\category;
use App\Models\ProductDetail;
use Carbon\Carbon;
class AdminproductController extends Controller
{
    public function index()
{
    if(Auth::check()) {
        $user = Auth::user();
        $admin = $user->admin;
        $allCategory = category::getall();
        $product = Product::GetAllProduct();
        if($admin == 1) {
            $allProducts = Product::getAllProduct();
           
            return view('Admin.product', [
                'Products' => $product,
                'allProducts' => $allProducts,
                'category' => $allCategory
            ]);
        }
        return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');
    } else {
        return Redirect::route('login');
    }
}

    
    public function addProduct()
    {   
        if(auth::check() && auth::user()->admin == 1 )
        {
            $allcategory = category::getall();
            return view('Admin.addproduct',['allcategory'=>$allcategory]);
        }else{
            return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');
        }
      
    }

    
    public function create(Request $request)
    {
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

        // Lấy dữ liệu từ request
        $data = [
            'name' => $request->input('productName'),
            'link' => $link,
            'color' => $request->input('productColor'),
            'money' => $request->input('productPrice'),
            'description' => $request->input('productDescription'),
            'hot' => $request->has('isFeatured') ? true : false,
            'created_at' => Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString(),
            'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString(),
            'quantyti' => $request->input('quantyti'),
            'loai' => $request->input('category_id'), 
        ];
        // Thêm sản phẩm mới vào cơ sở dữ liệu
        $product = Product::addProduct($data);
        
        // Kiểm tra kết quả
        if ($product) {
            // Nếu thành công, chuyển hướng về trang tạo sản phẩm với thông báo thành công
            return redirect()->route('admin_addproduct_create')->with('success', 'Product created successfully');
        } else {
            // Nếu có lỗi, chuyển hướng về trang tạo sản phẩm với thông báo lỗi
            return redirect()->back()->with('error', 'Failed to create product');
        }
    }


    public function delete($id){
        if(auth::check() && auth::user()->admin == 1 )
        {
            $delete = Product::deleteProduct($id);
            $deleteD = ProductDetail::deleteProduct($id);
            if( $delete && $deleteD ){
                return redirect()->route('admin_product')->with('success', 'Product has been successfully deleted');
            }
        }else{
            return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');
        }
    }

    public function search(Request $request){
        if(auth::check() && auth::user()->admin == 1 ){
            $name = $request->input('search');
            $allname = Product::getnameProduct($name);
            $totalProducts = $allname->count();
            if($allname){
                return view('Admin.searchProduct',['allProducts'=> $allname],['totalProducts'=>$totalProducts]);
            }
        }else{
            return redirect()->route('login')->with('error', 'bạn không phải là người quản trị');

        }
    }

}
