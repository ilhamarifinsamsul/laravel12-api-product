<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * index
     * 
     * @return void
     */

    public function index()
    {
        //get all products
        $products = Product::latest()->paginate(5);

        //return collection of products as a resource
        return new ProductResource(true, 'List Data Products', $products);
    }

    /**
      * store
      *
      * @param mixed $request
      * @return void
      */

    public function store(Request $request)
    {
        // define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('products', $image->hashName());

        // create product
        $product = Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        // return response
        return new ProductResource(true, 'Data Product Berhasil Ditambahkan!', $product);
    }

    /**
     * show
     * 
     * @param mixed $id
     * @return void
     */

    public function show($id)
    {
        // find product by id
        $product = Product::find($id);

        // return single product as a resource
        return new ProductResource(true, 'Detail Data Product!', $product);
    }

    /**
     * update
     * 
     * @param mixed $request
     * @param mixed $id
     * @return void
     */

    public function update(Request $request, $id)
    {
        // find product by id
        $product = Product::findOrFail($id);

        // define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpg,jpeg,png,svg|max:2048',
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // check if image is not empty
        if ($request->file('image')) {
            
            // delete old image
            Storage::delete('products/' . basename($product->image));

            // upload new image
            $image = $request->file('image');
            $image->storeAs('products', $image->hashName());

            // update product with new image
            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);
        } else {
            // update product without new image
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);
        }

        // return response
        return new ProductResource(true, 'Data Product Berhasil diupdate!', $product);
    }
}
