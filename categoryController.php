<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\category;
use Illuminate\Http\Request;

class categoryController extends Controller
{
  public function index($id){
    $category = Product::getCategory($id);
    return view('Admin.category',['category'=>$category]);
  }  
  public function create(){
    // $category = Product::getCategory();
    return view('Admin.addcategory');
  }   
  // CategoryController.php

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create a new category record
        Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Redirect back with a success message
        return redirect()->route('admin_product')->with('success', 'Category created successfully!');
    }


}
