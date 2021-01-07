<?php

namespace App\Jobs;

use App\Models\Mutasi_rekening;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class MutasiCsvProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $header;
    public $fieldTable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header)
    {
        $this->data         = $data;
        $this->header       = $header;
        $this->fieldTable         = ['rekening', 'kode_transaksi', 'tgl_transaksi', 'tgl_efektif', 'tgl_efektif_dc', 'debit', 'kredit', 'saldo', 'description', 'copy_row', 'insert_user', 'show', 'compare_row', 'created_at'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->data as $mutasi){
            if(count($this->header) == count($mutasi) && $mutasi[0] != 'Account No'){
                $dateSet			= explode("/", substr($mutasi[1], 0, 8));
                $TimeSet			= explode(" ", $mutasi[1]);
                if(count($dateSet) == 3){
                    $getDate			= "20".$dateSet[2]."-".$dateSet[1]."-".$dateSet[0];
                    $DatePosting		= "20".$dateSet[2]."-".$dateSet[1]."-".$dateSet[0]." ".$TimeSet[1];
                }

                //get tgl posting
                $dateSet1			= explode("/", substr($mutasi[2], 0, 8));
                $TimeSet1			= explode(" ", $mutasi[2]);
                if(count($dateSet1) == 3){
                    $getDate1			= "20".$dateSet[2]."-".$dateSet[1]."-".$dateSet[0];
                    $DateVal			= "20".$dateSet[2]."-".$dateSet[1]."-".$dateSet[0]." ".$TimeSet[1];
                }

                $rekening_bni		= $mutasi[0];
                $tglPosting			= isset($DatePosting) ? $DatePosting : null;
                $valDate			= isset($DateVal) ? $DateVal : null;
                $branch				= $mutasi[3];
                $jurnalNo			= $mutasi[4];
                $deskripsi			= $mutasi[5];
                $debit				= preg_replace("/[^0-9.]/", "", $mutasi[6]);
                $kredit				= preg_replace("/[^0-9.]/", "", $mutasi[7]);
                $saldo				= NULL;

                $copy_row_bni		= implode(",",$mutasi);
                $compare_row		= "'".preg_replace("/[^0-9A-Za-z#]/", "", $rekening_bni.$jurnalNo.$valDate."DB#".round($debit)."CR#".round($kredit)."#S#".round($saldo))."'";

                $data_arr_row 	= [$rekening_bni, $jurnalNo, $tglPosting, $valDate, $valDate, $debit, $kredit, $saldo, $deskripsi, $copy_row_bni, 1, 1, $compare_row, date('Y-m-d H:i:s')];

                $mutasiData     = array_combine($this->fieldTable, $data_arr_row);

                $compareTask    = Mutasi_rekening::where('compare_row', $compare_row)->count();

                Mutasi_rekening::create($mutasiData);

            }
        }
    }

    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}
