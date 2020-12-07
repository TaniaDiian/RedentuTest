<?php

namespace App\Http\Services;

use App\Filter;
use App\Models\Category;
use App\Models\Products;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ImportService extends ServiceProvider
{
    public static function store($array)
    {
        $importProduct = [];
        $repeted = 0;
        $coletionMaxIndex = 9;
        $model_code_index = 5;
        $categoryArray = [];
        $maped_categories = [];

        $rows = $array[0];

        for ($i = 1; $i < count($rows); $i++) {

            $row = $rows[$i];

            $count = count($row);
            $notEmtptyIndex = self::notEmtptyIndex($row);
            $sliceIndex =  $notEmtptyIndex - $coletionMaxIndex;
            $splitedArray = array_slice($row, $sliceIndex, 10);

            $row_model_code =  $splitedArray[$model_code_index];

            if (array_key_exists((string) $row_model_code,  $importProduct)) {
                $repeted++;
            } else {

                if ($splitedArray[9] == 'есть в наличие') {
                    $availability = true;
                } else {
                    $availability = false;
                }

                $categories = [];
                for ($j = 0; $j < 3; $j++) {
                    if (!empty($splitedArray[$j])) {
                        array_push($categories, $splitedArray[$j]);
                        if (!in_array($splitedArray[$j], $categoryArray)) {
                            $categoryArray[$splitedArray[$j]] = [
                                "name" => $splitedArray[$j]
                            ];
                        }
                    }
                }

                $importProduct[$row_model_code] = 
                    self::returnStructure($splitedArray, $availability, $categories);

            }
            
        }
        
        if (!empty($categoryArray)) {
            self::saveCategory($categoryArray);
        }

        $stored = [];
        if (!empty($importProduct)) {
            $stored=   self::saveProducts($importProduct, $maped_categories);
        }

        return [
            'repeted' => $repeted,
            'stored'=> $stored,
            'total_records'=> count($rows) - 1 
        ];
    }


    private static function notEmtptyIndex($row)
    {
        $notEmtptyIndex = 0;
        foreach ($row as $key => $value) {
            if (!empty($value)) {
                $notEmtptyIndex  = $key;
            }
        }
        return $notEmtptyIndex;
    }

    private static function saveCategory($categoryArray){
        Category::insert(array_values($categoryArray));
        $stored_categories = Category::all()->toArray();

        foreach ($stored_categories as $stored_categorie) {
            $maped_categories[$stored_categorie['name']] = $stored_categorie;
        }
    }

    private static function saveProducts($importProduct, $maped_categories){

        $already_in_DB = 0;
        $saved = 0;
        foreach ($importProduct as $key_prod => $importProductValue) {
            $product = new Products();

            $product->model_code = $importProductValue['model_code'];
            $product->manufacturer = $importProductValue['manufacturer'];
            $product->name = $importProductValue['name'];
            $product->description = $importProductValue['description'];
            $product->price = $importProductValue['price'];
            $product->warranty = $importProductValue['warranty'];
            $product->availability = $importProductValue['availability'];

            $categories_ids = [];
            foreach ($importProductValue['categories'] as  $value_categories) {
                if (array_key_exists($value_categories, $maped_categories)) {
                    array_push($categories_ids, $maped_categories[$value_categories]['id']);
                }
            }
           try {
               //code...
               $product->save();
               $product->category()->attach($categories_ids);
               $saved ++;
           } catch (\Throwable $th) {
               //throw $th;
            //    var_dump($th);
            $already_in_DB ++;
           }
        }

        return [
          'already_in_DB'=> $already_in_DB,
          'saved'=> $saved
        ];
    }

    private static function returnStructure($splitedArray, $availability, $categories){
        $result = [
            'manufacturer' => $splitedArray[3],
            'name' => $splitedArray[4],
            'model_code' => $splitedArray[5],
            'description' => $splitedArray[6],
            'price' => $splitedArray[7],
            'warranty' => $splitedArray[8],
            'availability' => $availability,
            'categories' => $categories
        ];

        return $result;
    }
}