<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Products;

class productController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
        $product = Products::with(array(
            'barcodes',
            'qltags' => function($query) {
                $query->with('tags');
            }
        ))
        ->where('product_id',$id)
        ->get();
        
        return response()->json($this->transformCollection($product),200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function transformCollection($products) {
        //Chuyển truy vấn dạng object thành mảng
        $productsToArray = $products->toArray();
        return [  
            'status' => 0,
            'messages' => 'Return success!',
            'data' => array_map([$this,'transformData'],$productsToArray)
        ];
    }
    public function transformData($products) {
        //$show = json_decode(json_encode($products));
        //Trả về định dạng cho dữ liệu
        return [
            'product_id' => $products['product_id'],
            'product_type' => $products['product_type'],
            'product_stock_number' => $products['product_stock_number'],
            'product_name' => $products['product_name'],
            'product_unit_string' => $products['product_unit_string'],
            'product_unit_quantity' => $products['product_unit_quantity'],
            'product_description' => $products['product_description'],
            'product_retail_price' => $products['product_retail_price'],
            'product_barcodes' => $this->collectBarcode($products['barcodes']),
            'product_ql_tags' => $this->collectQLTag($products['qltags'])
        ];
    }
    public function collectBarcode($products) {
        //Tạo một mảng để chứa các $barcode của $product
        $arr = [];
        for ($i=0; $i < count($products); $i++) { 
            # code...
            //Tạo một đối tượng $bar để lưu trữ một barcode
            //Một $products sẽ có nhiều $bar
            $bar = new class{};
            $bar->barcode_id = $products[$i]['barcode_id'];
            $bar->barcode_product_id = $products[$i]['barcode_product_id'];
            $bar->barcode_name = $products[$i]['barcode_name'];
            $bar->barcode_img = $products[$i]['barcode_img'];
            array_push($arr,$bar);
        }
        return $arr;
    }
    public function collectQLTag($products) {
        //Tạo một mảng để chứa các $qltag của $product
        $arr = [];
        for ($i = 0;$i < count($products);$i++) {
            //Tạo một đối tượng $qltag để lưu trữ một qltag
            //Một $products sẽ có nhiều $qltag
            $qltag = new class{};
            $qltag->ql_tags_id = $products[$i]['ql_tags_id'];
            $qltag->ql_tags_product_id = $products[$i]['ql_tags_product_id'];
            $qltag->ql_tags_tag_id = $products[$i]['ql_tags_tag_id'];
            $qltag->tags = $this->collectTag($products[$i]['tags']);
            //Thêm đối tượng $qltag
            array_push($arr,$qltag);
        }
        return $arr;
    }
    public function collectTag($tag) {
        //Trả về một đối tượng tag
        $tagObj = new class {};
        $tagObj->tag_id = $tag['tag_id'];
        $tagObj->tag_name = $tag['tag_name'];
        return $tagObj;
    }
}
