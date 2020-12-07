<?php

namespace App\Http\Controllers;

use App\Imports\ProductssImport;
use App\Models\Products;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Services\ImportService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use App\Rules\MaxServerUploadFile;

class ImportProductsController extends Controller
{
   public function importProducts(Request $request)
   {

      ini_set('max_execution_time', 10);

      $request->validate([
         'file' => ['required', 'mimes:xls,xlsx', new MaxServerUploadFile],
     ]);

      $time_start = microtime(true);
      if ($request->hasFile('file')) {
         $data = Excel::toArray(new ProductssImport, request()->file('file'));
      }

      $result = ImportService::store($data);
      $time_end = microtime(true);
      $execution_time = ($time_end - $time_start);
      $message = 'Products was imported';

      $result['execution_time'] =  round($execution_time, 2);

      return  View::make('success')
         ->with('data', $result)
         ->with('message', $message);

      // print("<pre>".print_r($result , true)."</pre>");
      // die();

   }
}
