<?php

namespace App\Http\Controllers;

use App\Jobs\MutasiCsvProcess;
use App\Models\Mutasi_rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class MutasiController extends Controller
{
    public function index()
    {
        return view('upload-file');
    }
    public function upload()
    {
        if(request()->has('mycsv')){

            $data       = file(request()->mycsv);

            $chunks         = array_chunk($data, 1000);
            $header = [];

            $batch = Bus::batch([])->dispatch();
            foreach($chunks as $key =>$chunk){
                $data = array_map('str_getcsv', $chunk);
                if($key === 0){
                    $header = $data[0];
                    unset($data[0]);
                }
                $batch->add(new MutasiCsvProcess($data, $header));
            }
            return $batch;
        }else{
            return 'please upload file !';
        }
    }

    public function batch()
    {
        $batchId = request('id');
        return Bus::findBatch($batchId);
    }
}
