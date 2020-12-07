<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Products;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductssImport implements ToModel
{   
    public function model(array $row){
        return $row;
    }
}
