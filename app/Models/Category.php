<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //use HasFactory;

    protected $fillable = [
        'id', 	
        'name', 	
        'alias',  
        'product_id',
        'category_id'
      ];

    public function product()
    {
        return $this->belongsToMany('App\Models\Product', 'category_product', 'category_id', 'product_id');
    }
}
