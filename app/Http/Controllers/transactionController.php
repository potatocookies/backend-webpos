<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transactions;


class transactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
        $transactions = Transactions::with(array(
            'qltransactions' => function($query) {
                $query->with('products');
            }
        ))
        ->where('transaction_id',$id)
        ->get();
        return response()->json($this->transformCollection($transactions),200);
        
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
        //require
        $transaction = $request->input('transaction');
        $transaction_type = $transaction->transaction_type;
        $transaction_products = $transaction->transaction_product;
        $transaction_id = Transactions::select('transaction_id')->max('transaction_id') + 1;
        if ($transaction_type === '' || empty($transaction_product)) {
            return response()->json([
                'error'=>[
                    'status'=>1
                    'message'=>'Cung cap du thong tin!'
                ]
            ],422);
        }
        //optional
        $transaction_supplier = $transaction->transaction_supplier;
        $transaction_ref = $transaction->transaction_ref;
        if ($transaction->transaction_remark === '') $transaction_remark = 'SUPREC-'.strval($transaction_id);
        else $transaction_remark = $transaction->transaction_ref;
        
        $transactions = new Transactions();
        $transactions->transaction_id = $transaction_id;
        $transactions->transaction_type = $transaction_type;
        $transactions->transaction_ref = $transaction_ref;
        $transactions->status = 'Posted';
        $transactions->save();

        $qlTransaction = new QLTransactions();
        for ($i = 0;$i < count($arrProduct);$i++) {
            $qlTransaction->ql_transactions_transaction_id = $transaction_id;
            $qlTransaction->ql_transactions_product_id = $arrProduct['product_id'];
            $qlTransaction->ql_transactions_quantity_bought = $arrProduct['product_quantity_bought'];
            $qlTransaction->save();
        }
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
        $transaction = Transactions::where('transaction_id',$id)
        ->first();
        if ($transaction['transaction_status'] === 'Posted') $transaction['transaction_status'] = 'Voided';
        $transaction->save();
        return [
            'status' => 0,
            'message' => 'Successful!'
        ];
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
    public function transformCollection($transaction) {
        $transactionToArray = $transaction->toArray();
        return [
            'status' => 0,
            'message' => 'Successful!',
            'data' => array_map([$this,'transform'],$transactionToArray)
        ];
    }
    public function transform($transaction) {
        return [
            'transaction_id' => $transaction['transaction_id'],
            'transaction_ref' => $transaction['transaction_ref'],
            'transaction_date' => $transaction['transaction_date'],
            'transaction_user' => $transaction['transaction_user'],
            'transaction_type' => $transaction['transaction_type'],
            'qltransactions' => $this->collectQLTransaction($transaction['qltransactions'])
        ];
    }
    public function collectQLTransaction($transaction) {
        $arr = [];
        for ($i = 0;$i < count($transaction);$i++) {
            $obj = new class{};
            $obj->ql_transactions_id = $transaction[$i]['ql_transactions_id'];
            $obj->ql_transactions_quantity_bought = $transaction[$i]['ql_transactions_quantity_bought'];
            $obj->products = $this->collectProducts($transaction[$i]['products']);
            array_push($arr,$obj);
        }
        return $arr;
    }
    public function collectProducts($transaction) {
        $obj = new class {};
        $obj->product_id = $transaction['product_id'];
        $obj->product_name = $transaction['product_name'];
        return $obj;
    }
}
