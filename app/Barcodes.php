<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barcodes extends Model
{
    //
    protected $table = 'barcodes';
    public function products() {
    	return $this->belongsTo('App\Products','barcode_product_id','product_id');
    }
}
