<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'buyerName', 'buyerCity', 'profit','sellerCode','buyerCode','phoneBuyer','commodity_id','price','created_at'
    ];

    public function commodities ()
    {
        return $this->belongsToMany(Commodity::class);
    }

    public function  supplier()
    {
       $supplier_id = $this->commodities->pluck('supplier_id');
       $supplier =Supplier::where('id',$supplier_id)->first();
        return  $supplier;
    }


}
