<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    protected $fillable = [
        'nameCommodity', 'codeCommodity', 'Supplier_id', 'imageUrl', 'priceCommodity', 'status', 'created_at','updated_at'
    ];

    public function scopeSearch($query,$key)
    {

        $query->where('nameCommodity','LIKE','%'.$key.'%')
            ->Orwhere('codeCommodity','LIKE','%'.$key.'%')
            ->Orwhere('priceCommodity','LIKE','%'.$key.'%');

        return $query;
    }


    public function Supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function Sales ()
    {
        return $this->belongsToMany(Sale::class);
    }






}