<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    //use HasFactory;

  protected $primaryKey = 'id';
  // public $incrementing = false;
  // protected $keyType = 'string';
  
  protected $fillable = [
    'id',
    'model_code',
    'manufacturer', 	
    'name', 	
    'description', 
    'price',  
    'warranty',  
    'availability',  
    'product_id',
    'category_id'
  ];


    public function category()
    {
      return $this->belongsToMany('App\Models\Category', 'category_product',  'product_id', 'category_id');
    }

}
