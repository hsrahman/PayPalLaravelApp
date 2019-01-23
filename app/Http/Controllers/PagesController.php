<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Basket;

class PagesController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth', ['except'=>['index']]);
    }

    function index(){
    	$title = "Home";
		$allProducts = Product::paginate(3);
    	return view('pages.welcome')->with("title", $title)->with("products", $allProducts);
    }

    function addItem(Request $request, $id){
        $validator = \Validator::make(array_merge(
            [
              'id'=>$id
            ], 
            $request->all()
        ), [
            "id" => 'required|numeric|exists:products,id'
        ])->validate();

        $basket = new Basket;
        $basket->user_id = auth()->user()->id;
        $basket->product_id = $id;
        $basket->save();

        return redirect("/");
    }

}
