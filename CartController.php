<?php

namespace App\Http\Controllers;
use App\Models\product;
use App\Models\cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Termwind\render;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        if (Auth::check()) {
        $user_id = Auth::id();
        $id = request('id');    
        $quantity = request('quantity');
        $dataProduct = product::getProductById($id);
       if($dataProduct){
        cart::addtocart($dataProduct->name,$dataProduct->link,$user_id,$dataProduct->money,$quantity,$dataProduct->product_id);
        $cart = cart::getCartUserID($user_id);
        session(['cart'=>$cart]);
        session(['dem'=>count($cart)]); 
        return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng thành công!');
       }else
       return redirect()->back()->with('error', 'Không tìm thấy sản phẩm!');
       }else{
        return redirect()->route('login');
       }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Auth::check()) {
            $allCart = cart::getCartUserID($id);
            return view('frontend.cart', ['allCart' => $allCart]);
        } else {
            return redirect()->route('login');
        }
    }
    
    // public function show($id)
    // {   
    // if (Auth::check()) {
    //     $allCart = cart::getCartUserID($id);
        
    //     // Truyền dữ liệu sang view frontend.cart
    //     $cartView = view('frontend.cart', ['allCart' => $allCart]);
        
    //     // Truyền dữ liệu sang view frontend.master
    //     $masterView = view('frontend.master', ['allCart' => $allCart]);
        
    //     // Trả về cả hai view
    //     return $cartView->with('masterView', $masterView);
    // } else {
    //     return redirect()->route('login');
    // }   
    // }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(cart $cart)
    {
        $id = request('id');
        if (Auth::check()) {
            if (Cart::deleteCart($id)) {
                $id = Auth::id(); 
                $cart = cart::getCartUserID($id);
                session(['cart'=> $cart]);
                session(['dem'=>count($cart)]);
                return redirect()->route('cart', ['id' => $id])->with('success', 'Giỏ hàng đã được xóa thành công');
            } else {
                return redirect()->route('cart', ['id' => $id])->with('error', 'không thể xóa giỏ hàng');
            }
        } else {
            return redirect()->route('login');
        }
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(cart $cart)
    {
        //
    }
}
