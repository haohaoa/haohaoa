<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $AllProduct = Product::GetAllProduct();
        // return view('frontend.home',compact('allProduct'));
        // return view('frontend.home')->with('allProduct',  $AllProduct);
        return view('frontend.home')->with(['allProduct' => $AllProduct]);


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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($product_id)
{
    $product_ctsp = Product::GetAllProduct();
    // Gọi phương thức của model Product để lấy dữ liệu sản phẩm dựa trên id
    $product = Product::getProductById($product_id);
    
    // Gọi phương thức của model ProductDetail để lấy dữ liệu chi tiết sản phẩm dựa trên product_id
    $detail = ProductDetail::getProductDetailById($product_id);
   
    // Kiểm tra xem sản phẩm và chi tiết sản phẩm có tồn tại hay không
    if ($product && $detail) {
        // Nếu cả hai tồn tại, trả về view và truyền dữ liệu sản phẩm và chi tiết sản phẩm
        return view('frontend.product', ['product' => $product, 'detail' => $detail,'product_ctsp'=>$product_ctsp]);
    } else {
        // Nếu không tìm thấy, trả về thông báo lỗi hoặc thực hiện hành động khác
        return redirect()->route('home')->with('error', 'Product not found.');
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
